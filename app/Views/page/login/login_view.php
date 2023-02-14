<!doctype html>
<html lang="es">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Bootstrap demo</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.3/font/bootstrap-icons.css">
  <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <style>
    #contentLogo {
      position: relative;
    }

    #backgroundLogin {
      background-image: url(/assets/img/login/background.jpeg);
      background-repeat: no-repeat;
      background-size: cover;
      background-position-x: center;
      height: 100vh;
    }

    #greenFilter {
      background-color: #27A11A7D;
      height: 100vh;
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      margin: 0px;
    }

    #logoWhats {
      height: 100vh;
      width: 100%;
      margin: 0 !important;
      top: 0;
      left: 0;
      position: absolute;
    }

    #logoWhatApi {
      display: flex;
      flex-direction: column;
      justify-content: center;
      align-items: center;
    }

    .coloGreyText {
      color: #575757 !important;
    }

    .greenButton {
      background: linear-gradient(90deg, #2ABF1A 0.03%, #27A11A 100.03%);
      --bs-btn-color: #fff;
      --bs-btn-hover-color: #fff;
      --bs-btn-focus-shadow-rgb: 49, 132, 253;
      --bs-btn-active-color: #fff;
      --bs-btn-active-shadow: inset 0 3px 5px rgba(0, 0, 0, 0.125);
      --bs-btn-disabled-color: #fff;
    }
  </style>
</head>

<body class="container-fluid">
  <div class="row">
    <div class="d-none d-sm-block col-sm-4 col-md-6 col-lg-7 col-xl-8" id="contentLogo">
      <div class="row" id="backgroundLogin"></div>
      <div class="row" id="greenFilter"></div>
      <div class="row" id="logoWhats">
        <div id="logoWhatApi">
          <img class="img-fluid" style="max-width: 388px; max-height: 409px;" src="/assets/img/login/logo.svg" alt="">
        </div>
      </div>
    </div>
    <div class="col-12 col-sm-8 col-md-6 col-lg-5 col-xl-4 d-flex justify-content-center align-items-center" style="height: 100vh;">
      <div class="container px-5 px-md-1 px-lg-3 px-xl-4 px-xxl-5">
        <div class="row pb-5" style="justify-content: center;">
          <img src="/assets/img/login/feclogo.png" alt="Fec" class="img-fluid" style="max-width: 326.09px; max-height: 58px;">
        </div>
        <div class="row">
          <div class="col-12 px-5">
            <h2 class="coloGreyText my-3">¡Hola! <span><img style="height: 2rem;" src="/assets/img/login/hand.png" alt="hand"></span></h2>
            <h5 class="coloGreyText my-3" style="font-weight: 400;">Bienvenido</h5>
            <div class="mt-4 mb-3">
              <input type="text" class="form-control form-control-lg" id="inputUser" placeholder="Nombre de usuario">
            </div>
            <div class="mt-4 mb-3 input-group">
              <input type="password" class="form-control form-control-lg" id="inputPass" placeholder="Contraseña">
            </div>

            <button type="button" class="mt-3 col-12 btn greenButton">Iniciar Sesión</button>
            <button type="button" class="mt-3 btn btn-link coloGreyText" style="text-decoration: none;">¿Olvidaste tu contraseña?</button>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="toast-container position-fixed bottom-0 end-0 p-3">
    <div id="toastDanger" class="toast text-bg-danger" role="alert" aria-live="assertive" aria-atomic="true">
      <div class="d-flex">
        <div class="toast-body">
          Hello, world! This is a toast message.
        </div>
        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4" crossorigin="anonymous"></script>
  <script>
    const inputUser = document.getElementById('inputUser');
    const inputPass = document.getElementById('inputPass');
    const btnLogin = document.querySelector('.greenButton');
    const toastDanger = document.getElementById('toastDanger')

    btnLogin.addEventListener('click', () => {
      if (inputUser.value === '' || inputPass.value === '') {
        let toastMessage = toastDanger.querySelector('.toast-body')
        toastMessage.innerHTML = 'Debes ingresar todos los campos'
        const toast = new bootstrap.Toast(toastDanger)
        toast.show()
      } else {
        loginProcess(inputUser.value, inputPass.value)
      }
    });

    function loginProcess(user, pass) {

      Swal.fire({
        title: 'Validando credenciales...',
        text: '',
        timerProgressBar: true,
        heightAuto: false,
        didOpen: () => {
          Swal.showLoading()
        },
      })

      var myHeaders = new Headers();
      myHeaders.append("Content-Type", "application/x-www-form-urlencoded");

      var urlencoded = new URLSearchParams();
      urlencoded.append("username", user);
      urlencoded.append("password", pass);

      var requestOptions = {
        method: 'POST',
        headers: myHeaders,
        body: urlencoded,
        redirect: 'follow'
      };


      fetch("/login", requestOptions)
        .then(response => response.json())
        .then(result => {
          console.log(result);
          if (result.status === 200) {
            if (result.susses) {
              window.location.href = result.url
            }else{
              Swal.close();
              let toastMessage = toastDanger.querySelector('.toast-body')
              toastMessage.innerHTML = result.message
              const toast = new bootstrap.Toast(toastDanger)
              toast.show()
            }
          } else {
            Swal.close();
            let toastMessage = toastDanger.querySelector('.toast-body')
            toastMessage.innerHTML = 'Usuario o contraseña incorrectos'
            const toast = new bootstrap.Toast(toastDanger)
            toast.show()
          }
        })
        .catch(error => {
          Swal.close();
          console.log('error', error);
          let toastMessage = toastDanger.querySelector('.toast-body')
          toastMessage.innerHTML = 'Ocurrió un error'
          const toast = new bootstrap.Toast(toastDanger)
          toast.show()
        });

    }
  </script>
</body>

</html>