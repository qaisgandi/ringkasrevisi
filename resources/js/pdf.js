import * as pdfjs from 'pdfjs-dist/build/pdf.mjs';
import pdfWorkerUrl from 'pdfjs-dist/build/pdf.worker.min.mjs?url';
pdfjs.GlobalWorkerOptions.workerSrc = pdfWorkerUrl;
window.pdfjsLib = { ...pdfjs, getDocument: source => pdfjs.getDocument({
    ...(typeof source === 'string' ? {url: source} : source), isEvalSupported: false, enableScripting: false,
}) };
