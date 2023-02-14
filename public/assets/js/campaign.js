
let currentPage = 1;

document.addEventListener("DOMContentLoaded", function(event) {
    getListCampaign()
});


function getListCampaign(cuPage = 1){
    const bodyTableCampaign = document.getElementById('bodyTableCampaign');

    var requestOptions = {
        method: 'POST',
        redirect: 'follow'
      };
      
      fetch(`/campaign/list/${cuPage}`, requestOptions)
        .then(response => response.json())
        .then(result => {
            console.log(result)
            if(result.susses && result.status == "200"){
                bodyTableCampaign.innerHTML = '';
                result.data.forEach(element => {
                    const tr = document.createElement('tr');
                    const td1 = document.createElement('td');
                    td1.classList.add('align-middle');
                    const div1 = document.createElement('div');
                    div1.classList.add('d-flex', 'align-items-center');
                    const img = document.createElement('img');
                    img.setAttribute('width', '45px');
                    img.setAttribute('height', '45px');
                    img.classList.add('img-fluid','imgCamping');
                    img.src = '/assets/img/dashboard/noimage.png';
                    const span1 = document.createElement('span');
                    span1.classList.add('ms-2', 'me-3', 'fw-normal',"col");
                    span1.innerHTML = element.titulo;
                    const button1 = document.createElement('button');
                    button1.classList.add('btn', 'btt-blue-send','buttonBlueMax');
                    button1.innerHTML = 'Mandar de nuevo';
                    const td2 = document.createElement('td');
                    td2.classList.add('align-middle');
                    const div2 = document.createElement('div');
                    div2.classList.add('d-flex', 'flex-wrap', 'flex-column');
                    const span2 = document.createElement('span');
                    span2.classList.add('fw-normal', 'ms-2', 'me-3');
                    span2.innerHTML = element.fecha_hora;
                    const td3 = document.createElement('td');
                    td3.classList.add('align-middle');
                    const div3 = document.createElement('div');
                    div3.classList.add('d-flex', 'flex-wrap', 'flex-column');
                    const span3 = document.createElement('span');
                    span3.classList.add('fw-normal');
                    span3.innerHTML = element.totalMensajes;
                    const span4 = document.createElement('span');
                    span4.classList.add('fw-normal');
                    span4.innerHTML = ' Mensajes abiertos';
                    const td4 = document.createElement('td');
                    td4.classList.add('align-middle');
                    const button2 = document.createElement('button');
                    button2.classList.add('btn', 'btt-green-circle');
                    button2.innerHTML = element.status;

                    bodyTableCampaign.appendChild(tr);
                    tr.appendChild(td1);
                    td1.appendChild(div1);
                    div1.appendChild(img);
                    div1.appendChild(span1);
                    div1.appendChild(button1);
                    tr.appendChild(td2);
                    td2.appendChild(div2);
                    div2.appendChild(span2);
                    tr.appendChild(td3);
                    td3.appendChild(div3);
                    div3.appendChild(span3);
                    div3.appendChild(span4);
                    tr.appendChild(td4);
                    td4.appendChild(button2);
                });

                currentPage = cuPage;
                let maxPage = result.pager;
                paginate(maxPage, '.pagination');

            }else{
                console.log('error', result)
                bodyTableCampaign.innerHTML = '';
                const tr = document.createElement('tr');
                const td = document.createElement('td');
                td.classList.add('align-middle','text-center');
                td.innerHTML = 'No hay campañas';
                td.setAttribute('colspan', '4');
                tr.appendChild(td);
                bodyTableCampaign.appendChild(tr);
                currentPage = 1;
                document.querySelector('.pagination').innerHTML = '';
            }
        })
        .catch(error => {
            bodyTableCampaign.innerHTML = '';
            bodyTableCampaign.innerHTML = '';
            const tr = document.createElement('tr');
            const td = document.createElement('td');
            td.classList.add('align-middle','text-center');
            td.innerHTML = 'No hay campañas';
            td.setAttribute('colspan', '4');
            tr.appendChild(td);
            bodyTableCampaign.appendChild(tr);
            currentPage = 1;
            document.querySelector('.pagination').innerHTML = '';
        });

}


function changePage(page){
    currentPage = page;
    loadingTableView();
    getListCampaign(page);
}


function loadingTableView() {
    const bodyTableCampaign = document.getElementById('bodyTableCampaign');
    bodyTableCampaign.innerHTML = '';
  
    for (let i = 0; i < 4; i++) {
      const tr = document.createElement('tr');
      tr.setAttribute('scope', 'row');
  
      for (let j = 0; j < 4; j++) {
        const td = document.createElement('td');
        const div = document.createElement('div');
        div.classList.add('row', 'gx-3');
  
        switch (j) {
          case 0:
            const div1 = document.createElement('div');
            const div2 = document.createElement('div');
            const div3 = document.createElement('div');
            div1.classList.add('rounded-3', 'shine','me-1');
            div2.classList.add('rounded-3', 'shine','me-1','col');
            div3.classList.add('rounded-3', 'shine','me-1','col');
            div1.setAttribute('style', 'height: 45px; width: 45px;');
            div2.setAttribute('style', 'height: 30px; width: 80px;');
            div3.setAttribute('style', 'height: 38px; width: 150px;');
            div.appendChild(div1);
            div.appendChild(div2);
            div.appendChild(div3);

            break;
          case 1:
            const div4 = document.createElement('div');
            div4.classList.add('rounded-3', 'col', 'shine', 'me-1');
            div4.setAttribute('style', 'height: 30px; width: 150px;');
            div.appendChild(div4);
            break;
          case 2:
            const div6 = document.createElement('div');
            div6.classList.add('rounded-3', 'col', 'shine', 'me-1');
            div6.setAttribute('style', 'height: 20px; width: 100px;');
            div.appendChild(div6);
            break;
          case 3:
            const div7 = document.createElement('div');
            div7.classList.add('rounded-3', 'col', 'shine', 'me-1');
            div7.setAttribute('style', 'height: 30px; width: 50px;');
            div.appendChild(div7);
            break;
          default:
            break;
        }
  
        td.setAttribute('colspan', '1');
        td.appendChild(div);
        tr.appendChild(td);
      }
  
      bodyTableCampaign.appendChild(tr);
    }
  }


const paginate = (pagesMax, selector) => {
    const currentPageLocal = currentPage;

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