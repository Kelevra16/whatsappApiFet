const paginate = (totalPages, currentPage,selector) => {
    let pagination = `<nav aria-label="Page navigation">
    <ul class="pagination">`;
  
    // Botón "Previous"
    if (currentPage == 1) {
        pagination += `
        <li class="page-item disabled">
            <span class="page-link">‹</span>
        </li>`
    }else{
        pagination += `
        <li class="page-item">
            <a href="#" class="page-link" onclick="changePage(${currentPage - 1})"> ‹ </a>
        </li>`
    }
  
    // Botones de páginas
    if (totalPages <= 7) {
      for (let i = 1; i <= totalPages; i++) {
        pagination += `
        <li class="page-item ${currentPage == i ? "active" : ""}">
            <a href="#" class="page-link" onclick="changePage(${i})">${i}</a>
        </li>`;
      }
    } else {
      if (currentPage <= 4) {
        for (let i = 1; i <= 5; i++) {
            pagination += `
            <li class="page-item ${currentPage == i ? "active" : ""}">
                <a href="#" class="page-link" onclick="changePage(${i})">${i}</a>
            </li>`;
        }
        pagination += `
        <li class="page-item disabled">
            <span class="page-link">...</span>
        </li>
        <li class="page-item">
            <a href="#" class="page-link" onclick="changePage(${totalPages})">${totalPages}</a>
        </li>`;
      } else if (currentPage >= totalPages - 3) {
        pagination += `
        <li class="page-item">
            <a href="#" class="page-link" onclick="changePage(1)">1</a>
        </li>
        <li class="page-item disabled">
            <span class="page-link">...</span>
        </li>`;

        for (let i = totalPages - 4; i <= totalPages; i++) {
          pagination += `
            <li class="page-item ${currentPage == i ? "active" : ""}">
                <a href="#" class="page-link" onclick="changePage(${i})">${i}</a>
            </li>`;
        }
      } else {
        pagination += `
        <li class="page-item">
            <a href="#" class="page-link" onclick="changePage(1)">1</a>
        </li>
        <li class="page-item disabled">
            <span class="page-link">...</span>
        </li>`;

        pagination += `
        <li class="page-item">
            <a href="#" class="page-link" onclick="changePage(${currentPage - 1})">${currentPage - 1}</a>
        </li>
        <li class="page-item active">
            <a href="#" class="page-link" onclick="changePage(${currentPage})">${currentPage}</a>
        </li>
        <li class="page-item">
            <a href="#" class="page-link" onclick="changePage(${currentPage + 1})">${currentPage + 1}</a>
        </li>`;

        pagination += `
        <li class="page-item disabled">
            <span class="page-link">...</span>
        </li>
        <li class="page-item">
            <a href="#" class="page-link" onclick="changePage(${totalPages})">${totalPages}</a>
        </li>`;
      }
    }
  
    // Botón "Next"
    if (currentPage < totalPages) {
      pagination += `
        <li class="page-item">
            <a href="#" class="page-link" onclick="changePage(${currentPage + 1})"> › </a>
        </li>`;
    }else{
        pagination += `
        <li class="page-item disabled">
            <span class="page-link">›</span>
        </li>`
    }
  
    document.querySelector(selector).innerHTML = pagination;
  }