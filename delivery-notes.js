// Excel upload functionality
document.getElementById('excelUpload').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (!file) return;
    
    const reader = new FileReader();
    reader.onload = function(e) {
        const data = new Uint8Array(e.target.result);
        const workbook = XLSX.read(data, {type: 'array', cellDates: true, cellNF: false, cellText: false});
        const firstSheet = workbook.Sheets[workbook.SheetNames[0]];
        
        displayExcelData(firstSheet, workbook);
        document.getElementById('uploadStatus').innerHTML = '<span style="color: green;">✓ File uploaded successfully</span>';
    };
    reader.readAsArrayBuffer(file);
});

function displayExcelData(worksheet, workbook) {
    const range = XLSX.utils.decode_range(worksheet['!ref']);
    let html = '<table class="excel-table">';
    
    for (let R = range.s.r; R <= range.e.r; R++) {
        html += '<tr>';
        for (let C = range.s.c; C <= range.e.c; C++) {
            const cellAddress = XLSX.utils.encode_cell({r: R, c: C});
            const cell = worksheet[cellAddress];
            
            let cellValue = '';
            if (cell) {
                if (cell.t === 'n' && cell.z) {
                    cellValue = XLSX.SSF.format(cell.z, cell.v);
                } else if (cell.t === 'd') {
                    cellValue = cell.w || cell.v;
                } else {
                    cellValue = cell.w || cell.v || '';
                }
            }
            
            const tag = R === 0 ? 'th' : 'td';
            html += `<${tag}>${cellValue}</${tag}>`;
        }
        html += '</tr>';
    }
    html += '</table>';
    
    document.getElementById('excelData').innerHTML = html;
}