// Load delivery notes data from database
async function loadDeliveryNotesData() {
    try {
        const response = await fetch('api/delivery-notes.php');
        const data = await response.json();
        
        if (data.success) {
            displayDeliveryNotesTable(data.data);
        }
    } catch (error) {
        console.error('Error loading delivery notes:', error);
    }
}

function displayDeliveryNotesTable(deliveryNotes) {
    let existingDatabaseData = document.querySelector('.database-data');
    
    let html = `
        <div class="database-data">
            <h3>Delivery Notes from Database</h3>
            <table class="excel-table">
                <thead>
                    <tr>
                        <th>DN No</th>
                        <th>Customer</th>
                        <th>Project</th>
                        <th>Status</th>
                        <th>Arrival Date</th>
                        <th>Site Address</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>`;
    
    deliveryNotes.forEach(note => {
        html += `<tr>
            <td>${note.dn_no || ''}</td>
            <td>${note.Customer || ''}</td>
            <td>${note.Project_Name || ''}</td>
            <td>${note.DN_Status || ''}</td>
            <td>${note.request_arrived_date ? new Date(note.request_arrived_date).toLocaleDateString() : ''}</td>
            <td>${note.Site_Address || ''}</td>
            <td><button class="view-btn" onclick="viewDeliveryDetails('${note.dn_no}')">View Details</button></td>
        </tr>`;
    });
    
    html += `</tbody></table></div>`;
    
    if (existingDatabaseData) {
        existingDatabaseData.outerHTML = html;
    } else {
        const uploadSection = document.querySelector('.upload-section');
        const uploadForm = document.getElementById('uploadForm');
        uploadForm.insertAdjacentHTML('afterend', html);
    }
    
    const excelUpload = document.getElementById('excelUpload');
    if (excelUpload && !excelUpload.hasAttribute('data-listener')) {
        excelUpload.addEventListener('change', handleExcelUpload);
        excelUpload.setAttribute('data-listener', 'true');
    }
}

function handleExcelUpload(e) {
    const file = e.target.files[0];
    if (!file) return;
    
    const reader = new FileReader();
    reader.onload = function(e) {
        const data = new Uint8Array(e.target.result);
        const workbook = XLSX.read(data, {type: 'array', cellDates: true, cellNF: false, cellText: false});
        const firstSheet = workbook.Sheets[workbook.SheetNames[0]];
        
        displayExcelData(firstSheet, workbook);
        saveExcelToDatabase(file.name, firstSheet, workbook);
        document.getElementById('uploadStatus').innerHTML = '<span style="color: green;">✓ File uploaded and saved to database</span>';
    };
    reader.readAsArrayBuffer(file);
}

function displayExcelData(worksheet, workbook) {
    const range = XLSX.utils.decode_range(worksheet['!ref']);
    let html = '<h3>Uploaded Excel Data</h3><table class="excel-table">';
    
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

function toggleUploadForm() {
    const form = document.getElementById('uploadForm');
    form.style.display = form.style.display === 'none' ? 'block' : 'none';
}

async function uploadDeliveryNote(formData) {
    try {
        const response = await fetch('api/upload-delivery-note.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify(formData)
        });
        
        const result = await response.json();
        
        if (result.success) {
            document.getElementById('uploadStatus').innerHTML = '<span style="color: green;">✓ Delivery note uploaded successfully</span>';
            document.getElementById('deliveryNoteForm').reset();
            toggleUploadForm();
            loadDeliveryNotesData();
        } else {
            document.getElementById('uploadStatus').innerHTML = '<span style="color: red;">✗ ' + result.error + '</span>';
        }
    } catch (error) {
        document.getElementById('uploadStatus').innerHTML = '<span style="color: red;">✗ Upload failed</span>';
    }
}

async function showSavedExcelSheets() {
    try {
        const response = await fetch('api/get-excel-sheets.php');
        const data = await response.json();
        
        if (data.success) {
            displaySavedSheetsList(data.sheets);
        }
    } catch (error) {
        console.error('Error loading saved sheets:', error);
    }
}

function displaySavedSheetsList(sheets) {
    let html = '<div class="saved-sheets"><h3>Saved Excel Sheets</h3><table class="excel-table"><thead><tr><th>Filename</th><th>Date</th><th>Action</th></tr></thead><tbody>';
    
    sheets.forEach(sheet => {
        html += `<tr>
            <td>${sheet.filename}</td>
            <td>${new Date(sheet.created_at).toLocaleDateString()}</td>
            <td><button class="view-btn" onclick="viewExcelSheet(${sheet.id})">View</button></td>
        </tr>`;
    });
    
    html += '</tbody></table></div>';
    document.getElementById('excelData').innerHTML = html;
    showBackButton();
}

async function viewExcelSheet(id) {
    try {
        const response = await fetch(`api/get-excel-data.php?id=${id}`);
        const data = await response.json();
        
        if (data.success) {
            displaySavedExcelData(data.sheet_data, data.merged_cells, data.filename);
        }
    } catch (error) {
        console.error('Error loading sheet data:', error);
    }
}

function displaySavedExcelData(sheetData, mergedCells, filename) {
    let html = `<div class="saved-excel-view"><h3>${filename}</h3><table class="excel-table">`;
    
    sheetData.forEach(row => {
        html += '<tr>';
        row.forEach(cell => {
            html += `<td>${cell.value}</td>`;
        });
        html += '</tr>';
    });
    
    html += '</table></div>';
    document.getElementById('excelData').innerHTML = html;
    showBackButton();
}

function performSearch() {
    const searchTerm = document.getElementById('searchInput').value.toLowerCase();
    if (!searchTerm) return;
    
    // Search in delivery notes
    const deliveryRows = document.querySelectorAll('.database-data tbody tr');
    let foundDelivery = false;
    
    deliveryRows.forEach(row => {
        const text = row.textContent.toLowerCase();
        if (text.includes(searchTerm)) {
            row.style.backgroundColor = '#ffeb3b';
            foundDelivery = true;
        } else {
            row.style.backgroundColor = '';
        }
    });
    
    // Search in Excel data
    const excelRows = document.querySelectorAll('.excel-table tbody tr');
    let foundExcel = false;
    
    excelRows.forEach(row => {
        const text = row.textContent.toLowerCase();
        if (text.includes(searchTerm)) {
            row.style.backgroundColor = '#ffeb3b';
            foundExcel = true;
        } else {
            row.style.backgroundColor = '';
        }
    });
    
    if (foundDelivery || foundExcel) {
        showBackButton();
        document.getElementById('uploadStatus').innerHTML = `<span style="color: green;">Found matches for "${searchTerm}"</span>`;
    } else {
        document.getElementById('uploadStatus').innerHTML = `<span style="color: red;">No matches found for "${searchTerm}"</span>`;
    }
}

function showBackButton() {
    document.querySelector('.back-btn').style.display = 'inline-flex';
}

async function viewDeliveryDetails(dnNo) {
    try {
        const response = await fetch(`api/get-delivery-details.php?dn_no=${dnNo}`);
        const data = await response.json();
        
        if (data.success) {
            displayDeliveryDetailsForm(data.data);
        }
    } catch (error) {
        console.error('Error loading delivery details:', error);
    }
}

function displayDeliveryDetailsForm(delivery) {
    let html = `<div class="delivery-details">
        <h3>Delivery Note Details - ${delivery.dn_no}</h3>
        <div class="details-grid">
            <div class="detail-group">
                <label>DN Number:</label>
                <input type="text" value="${delivery.dn_no || ''}" readonly>
            </div>
            <div class="detail-group">
                <label>Customer:</label>
                <input type="text" value="${delivery.Customer || ''}" readonly>
            </div>
            <div class="detail-group">
                <label>Project Name:</label>
                <input type="text" value="${delivery.Project_Name || ''}" readonly>
            </div>
            <div class="detail-group">
                <label>Site Code:</label>
                <input type="text" value="${delivery.site_Code || ''}" readonly>
            </div>
            <div class="detail-group">
                <label>Customer PO:</label>
                <input type="text" value="${delivery.Customer_PO || ''}" readonly>
            </div>
            <div class="detail-group">
                <label>Customer Tel:</label>
                <input type="text" value="${delivery.Customer_Tel || ''}" readonly>
            </div>
            <div class="detail-group">
                <label>Project Code:</label>
                <input type="text" value="${delivery.Project_Code || ''}" readonly>
            </div>
            <div class="detail-group">
                <label>Product Category:</label>
                <input type="text" value="${delivery.Product_Category || ''}" readonly>
            </div>
            <div class="detail-group">
                <label>Product Manager:</label>
                <input type="text" value="${delivery.Product_Manager || ''}" readonly>
            </div>
            <div class="detail-group">
                <label>Site Address:</label>
                <textarea readonly>${delivery.Site_Address || ''}</textarea>
            </div>
            <div class="detail-group">
                <label>Receiver Name:</label>
                <input type="text" value="${delivery.receiver_name || ''}" readonly>
            </div>
            <div class="detail-group">
                <label>Receiver Tel:</label>
                <input type="text" value="${delivery.receiver_tel || ''}" readonly>
            </div>
            <div class="detail-group">
                <label>Status:</label>
                <input type="text" value="${delivery.DN_Status || ''}" readonly>
            </div>
            <div class="detail-group">
                <label>Created At:</label>
                <input type="text" value="${delivery.created_at ? new Date(delivery.created_at).toLocaleString() : ''}" readonly>
            </div>
        </div>
    </div>`;
    
    document.getElementById('excelData').innerHTML = html;
    showBackButton();
}

function goBack() {
    document.querySelectorAll('tr').forEach(row => {
        row.style.backgroundColor = '';
    });
    
    document.getElementById('searchInput').value = '';
    document.querySelector('.back-btn').style.display = 'none';
    
    loadDeliveryNotesData();
    
    document.getElementById('uploadStatus').innerHTML = '';
    document.getElementById('excelData').innerHTML = '';
}

async function saveExcelToDatabase(filename, worksheet, workbook) {
    try {
        const range = XLSX.utils.decode_range(worksheet['!ref']);
        const sheetData = [];
        const mergedCells = worksheet['!merges'] || [];
        
        // Extract all cell data with formatting
        for (let R = range.s.r; R <= range.e.r; R++) {
            const row = [];
            for (let C = range.s.c; C <= range.e.c; C++) {
                const cellAddress = XLSX.utils.encode_cell({r: R, c: C});
                const cell = worksheet[cellAddress];
                
                let cellData = {
                    address: cellAddress,
                    value: '',
                    type: '',
                    format: ''
                };
                
                if (cell) {
                    cellData.value = cell.w || cell.v || '';
                    cellData.type = cell.t || '';
                    cellData.format = cell.z || '';
                }
                
                row.push(cellData);
            }
            sheetData.push(row);
        }
        
        const payload = {
            filename: filename,
            sheet_data: sheetData,
            merged_cells: mergedCells
        };
        
        const response = await fetch('api/save-excel.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify(payload)
        });
        
        const result = await response.json();
        if (!result.success) {
            console.error('Failed to save Excel to database:', result.error);
        }
        
    } catch (error) {
        console.error('Error saving Excel to database:', error);
    }
}

// Load data when page loads
document.addEventListener('DOMContentLoaded', function() {
    loadDeliveryNotesData();
    
    document.getElementById('deliveryNoteForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = {
            dn_no: document.getElementById('dnNo').value,
            customer: document.getElementById('customer').value,
            project_name: document.getElementById('projectName').value,
            site_address: document.getElementById('siteAddress').value
        };
        
        uploadDeliveryNote(formData);
    });
});