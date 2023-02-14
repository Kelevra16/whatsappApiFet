let currentPage = 1;
let maxPageG = 0;

document.addEventListener("DOMContentLoaded", function(event) {
    getListDifucion();
});


function getListDifucion(cuPage = 1) {
    const groupListDifu = document.getElementById('bodyDifusionList');
    var myHeaders = new Headers();

    var requestOptions = {
      method: 'POST',
      headers: myHeaders,
      redirect: 'follow'
    };

    currentPage = cuPage;
    // maxPageG = result.pager;
    paginate(maxPageG, '.pagination');

    fetch(`/difusion/listDifusion/${cuPage}`, requestOptions)
      .then(response => response.json())
      .then(result => {
        console.log(result)
        if (result.susses && result.status == 200) {
          currentPage = cuPage;
          maxPageG = result.pager;
          paginate(maxPageG, '.pagination');

          groupListDifu.innerHTML = '';
            result.data.forEach(element => {
                const div1 = document.createElement('div');
                div1.classList.add('col-12','col-md-6','col-lg-4','col-xl-4','col-xxl-3');
                const card = document.createElement('div');
                card.classList.add('card','h-100');
                const cardBody = document.createElement('div');
                cardBody.classList.add('card-body', 'p-4', 'm-2');
                const row1 = document.createElement('div');
                row1.classList.add('row', 'mb-3');
                const col1 = document.createElement('div');
                col1.classList.add('col-4', 'text-center');
                const img = document.createElement('img');
                img.classList.add('img-fluid');
                img.src = '/assets/img/difusion/people.png';
                const col2 = document.createElement('div');
                col2.classList.add('col-8');
                const h4 = document.createElement('h4');
                h4.classList.add('fw-normal');
                h4.innerHTML = element.nombre;
                const h6 = document.createElement('h6');
                h6.classList.add('fw-normal', 'text-grey-geo');
                h6.innerHTML = element.totalContactos + ' contactos';
                const row2 = document.createElement('div');
                row2.classList.add('row', 'mb-3');
                const col3 = document.createElement('div');
                col3.classList.add('col-12');
                const p1 = document.createElement('p');
                p1.classList.add('text-blue-dart');
                p1.innerHTML = element.descripcion;
                const row3 = document.createElement('div');
                row3.classList.add('row', 'mb-3');
                const col4 = document.createElement('div');
                col4.classList.add('col');
                const p2 = document.createElement('p');
                p2.classList.add('text-grey-geo');
                const i = document.createElement('i');
                i.classList.add('bi', 'bi-geo-alt-fill');
                p2.innerHTML = i.outerHTML + ' ' + element.location;
                const row4 = document.createElement('div');
                row4.classList.add('row');
                const col5 = document.createElement('div');
                col5.classList.add('col-12');
                const a1 = document.createElement('a');
                a1.classList.add('btn', 'btt-blue-send','me-2','mb-2');
                a1.innerHTML = 'Enviar mensaje';
                a1.setAttribute('onclick', 'createdCampaing(' + element.id + ')');
                const a2 = document.createElement('a');
                a2.classList.add('btn', 'btt-blue-edit','me-2','mb-2');
                a2.innerHTML = 'Editar';
                a2.setAttribute('onclick', 'editList(' + element.id + ')');

                col1.appendChild(img);
                col2.appendChild(h4);
                col2.appendChild(h6);
                row1.appendChild(col1);
                row1.appendChild(col2);
                col3.appendChild(p1);
                row2.appendChild(col3);
                col4.appendChild(p2);
                row3.appendChild(col4);
                col5.appendChild(a1);
                col5.appendChild(a2);
                row4.appendChild(col5);
                cardBody.appendChild(row1);
                cardBody.appendChild(row2);
                cardBody.appendChild(row3);
                cardBody.appendChild(row4);
                card.appendChild(cardBody);
                div1.appendChild(card);
                groupListDifu.appendChild(div1);
            });
        }else{
        groupListDifu.innerHTML = '';
        console.log('error');

        const div1 = document.createElement('div');
        div1.classList.add('col-12', 'd-flex', 'justify-content-center', 'align-items-center');
        div1.style.height = '100%';
        const p = document.createElement('p');
        p.innerHTML = 'No hay listas de difusión';
        div1.appendChild(p);
        groupListDifu.appendChild(div1);
        currentPage = 1;
        document.querySelector('.pagination').innerHTML = '';

        }
      })
      .catch(error => {
        console.log('error', error)
        groupListDifu.innerHTML = '';
        const div1 = document.createElement('div');
        div1.classList.add('col-12', 'd-flex', 'justify-content-center', 'align-items-center');
        div1.style.height = '100%';
        const p = document.createElement('p');
        p.innerHTML = 'No hay listas de difusión';
        div1.appendChild(p);
        groupListDifu.appendChild(div1);
        currentPage = 1;
        document.querySelector('.pagination').innerHTML = '';
    });
    
}

function changePage(page){
  currentPage = page;
  loadingTableView();
  getListDifucion(page);
}

function loadingTableView() {
  const bodyTableCampaign = document.getElementById('bodyDifusionList');
  bodyTableCampaign.innerHTML = '';

  let html = `
  <div class="col-12 col-md-6 col-lg-4 col-xl-4 col-xxl-3">
  <div class="card shine" style="height: 350px; width: 100%; background-repeat-y: repeat;"></div>
</div>

<div class="col-12 col-md-6 col-lg-4 col-xl-4 col-xxl-3">
  <div class="card shine" style="height: 350px; width: 100%; background-repeat-y: repeat;"></div>
</div>

<div class="col-12 col-md-6 col-lg-4 col-xl-4 col-xxl-3">
  <div class="card shine" style="height: 350px; width: 100%; background-repeat-y: repeat;"></div>
</div>
  `;

  bodyTableCampaign.innerHTML = html;

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

function editList($idList) {
    console.log($idList);
    window.location.href = '/difusion/edit/' + $idList;
}