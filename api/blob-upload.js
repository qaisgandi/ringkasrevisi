import { handleUpload } from '@vercel/blob/client';
import { createHmac, timingSafeEqual } from 'node:crypto';

function verifyAdminUploadToken(token) {
    if (typeof token !== 'string' || !process.env.APP_KEY) return false;
    const [claims, signature, ...extra] = token.split('.');
    if (!claims || !signature || extra.length) return false;

    const expected = createHmac('sha256', process.env.APP_KEY).update(claims).digest('hex');
    const actualBuffer = Buffer.from(signature, 'hex');
    const expectedBuffer = Buffer.from(expected, 'hex');
    if (actualBuffer.length !== expectedBuffer.length || !timingSafeEqual(actualBuffer, expectedBuffer)) return false;

    try {
        const payload = JSON.parse(Buffer.from(claims, 'base64url').toString('utf8'));
        return payload.role === 'admin'
            && Number.isInteger(payload.uid)
            && payload.uid > 0
            && Number.isInteger(payload.exp)
            && payload.exp > Math.floor(Date.now() / 1000)
            && payload.exp <= Math.floor(Date.now() / 1000) + 240
            && typeof payload.nonce === 'string';
    } catch {
        return false;
    }
}

export default async function handler(request, response) {
    if (request.method !== 'POST') {
        return response.status(405).json({ error: 'Method not allowed' });
    }
    if (!process.env.BLOB_READ_WRITE_TOKEN) {
        return response.status(503).json({ error: 'Blob storage is not configured' });
    }

    try {
        const body = request.body ?? {};
        // @vercel/blob/client sends the authorization inside its event payload:
        // { type: "blob.generate-client-token", payload: { clientPayload } }.
        // Validate that value before handing the event to handleUpload.
        const clientPayload = body.payload?.clientPayload ?? body.clientPayload;
        if (!verifyAdminUploadToken(clientPayload)) {
            return response.status(403).json({ error: 'Upload authorization expired or invalid' });
        }

        const result = await handleUpload({
            request,
            body,
            onBeforeGenerateToken: async (_pathname, clientPayload) => {
                if (!verifyAdminUploadToken(clientPayload)) {
                    throw new Error('Upload authorization expired or invalid');
                }
                return {
                    allowedContentTypes: ['application/pdf'],
                    maximumSizeInBytes: 25 * 1024 * 1024,
                    addRandomSuffix: true,
                    tokenPayload: 'admin-publication-upload',
                };
            },
            onUploadCompleted: async () => {},
        });

        return response.status(200).json(result);
    } catch {
        return response.status(400).json({ error: 'PDF upload could not be authorized' });
    }
}
