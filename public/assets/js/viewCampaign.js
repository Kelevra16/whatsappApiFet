let currentPage = 1;
let maxPageG = 0;

document.addEventListener("DOMContentLoaded", function(event) {
    getContacts();
});


function getContacts(cuPage = 1) {
    const idCampaign = document.getElementById('idCampaign');
    const bodyTableCampaign = document.getElementById('bodyTableCampaign');

    currentPage = cuPage;
    paginate(maxPageG, '.pagination');
    var myHeaders = new Headers();
    myHeaders.append("Content-Type", "application/x-www-form-urlencoded");

    var urlencoded = new URLSearchParams();
    urlencoded.append("idCampaign", idCampaign.value);

    var requestOptions = {
        method: 'POST',
        headers: myHeaders,
        redirect: 'follow',
        body: urlencoded
    };

    fetch(`/campaign/view/contactsList/${cuPage}`, requestOptions)
        .then(response => response.json())
        .then(result => {
            console.log(result);
            bodyTableCampaign.innerHTML = '';
            if (result.status == 200 && result.susses) {
                currentPage = cuPage;
                maxPageG = result.page;
                paginate(maxPageG, '.pagination');

                result.data.forEach(element => {
                    const content = `<tr scope="row" >
                    <td colspan="1">
                        <p class="text-center">${element.lada}</p>
                    </td>
                    <td colspan="1">
                        <p class="text-center">${element.phone}</p>
                    </td>
                    <td colspan="1">
                        <p class="text-center">${element.name}</p>
                    </td>
                    <td colspan="1">
                        <p class="text-center">${element.status}</p>
                    </td>
                </tr>`;
                    bodyTableCampaign.innerHTML += content;
                });

            }else{
                const content = `<tr scope="row" >
                <td colspan="4">
                    <p class="text-center">Sin contactos</p>
                </td>
            </tr>`;
                bodyTableCampaign.innerHTML += content;
                paginate(0, '.pagination');

            }
        })
        .catch(error => {
            const content = `<tr scope="row" >
            <td colspan="4">
                <p class="text-center">Sin contactos</p>
            </td>
        </tr>`;
            bodyTableCampaign.innerHTML += content;
            paginate(0, '.pagination');
        });
        
}


function changePage(page){
    currentPage = page;
    loadingTableView();
    getContacts(page);
}

function loadingTableView(){
    const bodyTableCampaign = document.getElementById('bodyTableCampaign');
    bodyTableCampaign.innerHTML = '';
    const content = `                    <tr scope="row" >
    <td colspan="1">
        <div class="row gx-3">
            <div class="col rounded-3 shine me-1"  style="height: 40px; width: 45px;"></div>
        </div>
    </td>
    <td colspan="1">
        <div class="row gx-3">
            <div class="rounded-3 col shine me-1"  style="height: 40px; width: 150px;"></div>
        </div>
    </td>
    <td colspan="1">
        <div class="row gx-3">
            <div class="rounded-3 col shine me-1"  style="height: 40px; width: 150px;"></div>
        </div>
    </td>
    <td colspan="1">
        <div class="row gx-3">
            <div class="rounded-3 col shine me-1"  style="height: 40px; width: 50px;"></div>
        </div>
    </td>
</tr>`;

    for (let i = 0; i < 5; i++) {
        bodyTableCampaign.innerHTML += content;
    }
}

const paginate = (pagesMax, selector) => {
    const currentPageLocal = currentPage;
  
    if (pagesMax <= 1) {
        document.querySelector(selector).innerHTML = '';
        return;
    }
  
    const pages = []
  
    if (pagesMax <= 6){
        for (let i = 1; i <= pagesMax; i++) {
            pages.push({
                page: i,
                current: currentPageLocal == i
            })
        }
    }else{
        if (currentPageLocal == 1){
            for (let i = 1; i <= 5; i++) {
                pages.push({
                    page: i,
                    current: currentPageLocal == i
                })
            }
        }else if (currentPageLocal == pagesMax){
            pagemin = ((pagesMax - 4) < 1) ? 1 : (pagesMax - 4);
            for (let i = pagemin; i <= pagesMax; i++) {
                pages.push({
                    page: i,
                    current: currentPageLocal == i
                })
            }
        }else{
            pageMint = (currentPageLocal - 2 < 1) ? 1 : (currentPageLocal - 2);
            pagePlus = ((currentPageLocal+3) > pagesMax) ? pagesMax : (currentPageLocal+3);
            for (let i = pageMint; i <= pagePlus; i++) {
                pages.push({
                    page: i,
                    current: currentPageLocal == i
                })
            }
        }
    }
  
  
    const html = `
    <nav aria-label="Page navigation">
        <ul class="pagination">
            ${currentPageLocal == 1 ? `
                <li class="page-item disabled">
                    <span class="page-link">‹</span>
                </li>
            ` : `
                <li class="page-item">
                    <a href="#" class="page-link" onclick="changePage(${currentPageLocal -1})" >‹</a>
                </li>
            `}
            ${pages.map((item, index) => `
                <li${item.current ? ' class="page-item active"' : ' class="page-item"'}>
                    ${item.current ? `
                        <span class="page-link">${item.page}</span>
                    ` : `
                        <a href="#" class="page-link" onclick="changePage(${item.page})">${item.page}</a>
                    `}
                </li>
            `).join('')}
            ${currentPageLocal == pagesMax ? `
                <li class="page-item disabled">
                    <span class="page-link">›</span>
                </li>
            ` : `
                <li class="page-item">
                    <a href="#" class="page-link" onclick="changePage(${currentPageLocal +1})">›</a>
                </li>
            `}
        </ul>
        </nav>
    `
  
    document.querySelector(selector).innerHTML = html
}