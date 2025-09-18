CREATE TABLE excel_sheets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    filename VARCHAR(255) NOT NULL,
    sheet_data LONGTEXT NOT NULL,
    merged_cells TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);