// delivery-notes.js - Enhanced Delivery Notes Management System

class DeliveryNotesManager {
  constructor() {
    this.deliveryNotes = [];
    this.excelSheets = [];
    this.currentView = "delivery_notes";
    this.searchResults = [];
    this.init();
  }

  init() {
    this.setupEventListeners();
    this.loadDeliveryNotes();
    this.loadSavedExcelSheets();
  }

  setupEventListeners() {
    // Excel upload
    document.getElementById("excelUpload").addEventListener("change", (e) => {
      this.handleExcelUpload(e);
    });

    // Form submission
    document
      .getElementById("deliveryNoteForm")
      .addEventListener("submit", (e) => {
        e.preventDefault();
        this.handleFormSubmit();
      });

    // Search functionality
    document.getElementById("searchInput").addEventListener("keypress", (e) => {
      if (e.key === "Enter") {
        this.performSearch();
      }
    });

    // Real-time search
    document.getElementById("searchInput").addEventListener("input", (e) => {
      if (e.target.value.length > 2) {
        this.performSearch();
      } else if (e.target.value.length === 0) {
        this.goBack();
      }
    });
  }

  async handleExcelUpload(event) {
    const file = event.target.files[0];
    if (!file) return;

    this.showStatus("Processing Excel file...", "info");

    try {
      const data = await this.readExcelFile(file);
      const extractedData = this.extractDeliveryNoteData(data);

      // Save to local storage and display
      // Save locally first
      this.saveExcelSheet(file.name, extractedData);
      this.displayExcelData(extractedData, file.name);

      // Send to server
      try {
        const response = await fetch("api/upload-delivery-note.php", {
          method: "POST",
          headers: {
            "Content-Type": "application/json",
          },
          body: JSON.stringify(extractedData),
        });

        const result = await response.json();
        if (result.success) {
          this.showStatus("Data saved to database successfully!", "success");
        } else {
          throw new Error(result.error || "Failed to save to database");
        }
      } catch (error) {
        console.error("Server error:", error);
        this.showStatus(
          "Failed to save to database: " + error.message,
          "error"
        );
      }
    } catch (error) {
      console.error("Error processing Excel:", error);
      this.showStatus("Error processing Excel file: " + error.message, "error");
    }
  }

  readExcelFile(file) {
    return new Promise((resolve, reject) => {
      const reader = new FileReader();
      reader.onload = (e) => {
        try {
          const data = new Uint8Array(e.target.result);
          const workbook = XLSX.read(data, {
            type: "array",
            cellStyles: true,
            cellFormulas: true,
            cellDates: true,
          });

          const firstSheetName = workbook.SheetNames[0];
          const worksheet = workbook.Sheets[firstSheetName];
          resolve(worksheet);
        } catch (error) {
          reject(error);
        }
      };
      reader.onerror = () => reject(new Error("Failed to read file"));
      reader.readAsArrayBuffer(file);
    });
  }

  extractDeliveryNoteData(worksheet) {
    const getCellValue = (cellRefs) => {
      const refs = Array.isArray(cellRefs) ? cellRefs : [cellRefs];
      for (const cellRef of refs) {
        const cell = worksheet[cellRef];
        if (cell && cell.v !== undefined && cell.v !== "") {
          return String(cell.v).trim();
        }
      }
      return "";
    };

    const formatDateTime = (dateValue) => {
      if (!dateValue) return "";

      if (
        typeof dateValue === "string" &&
        dateValue.match(/^\d{4}-\d{2}-\d{2}/)
      ) {
        return dateValue;
      }

      if (typeof dateValue === "number") {
        try {
          const excelDate = XLSX.SSF.parse_date_code(dateValue);
          if (excelDate) {
            return `${excelDate.y}-${String(excelDate.m).padStart(
              2,
              "0"
            )}-${String(excelDate.d).padStart(2, "0")}`;
          }
        } catch (e) {
          console.warn("Date parsing error:", e);
        }
      }

      return dateValue.toString();
    };

    return {
      id: Date.now(),
      print_date:
        getCellValue(["B2", "A2"]) || new Date().toISOString().slice(0, 10),
      dn_no: getCellValue(["O1", "N1", "P1"]) || this.generateDNNumber(),
      mr_no: getCellValue(["O2", "N2", "P2"]) || "",
      purpose_of_delivery: getCellValue(["C3", "D3", "E3"]) || "",
      delivery_address: getCellValue(["C4", "D4", "E4"]) || "",
      site_code: getCellValue(["C5", "D5", "E5"]) || "",
      contract_info: getCellValue(["C6", "D6", "E6"]) || "",
      project_name: getCellValue(["C7", "D7", "E7"]) || "",
      product_category: getCellValue(["C8", "D8", "E8"]) || "",
      special_unloading_req: getCellValue(["C9", "D9", "E9"]) || "",
      installation_environment,
    };
  }
}
