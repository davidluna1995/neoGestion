<template>
  <div class="mt-4">
    <b-container class="bv-example-row">
      <div class="col-12 col-lg-12">
        <b-card class="text-center transparencia mb-4">
          <b-alert show variant="warning">
            <b>NOTA:</b>
            <em>
              Al cambiar algun dato de estos formularios por seguridad la
              sesión se cerrará automaticamente para que efectue un nuevo
              inicio de sesión con los nuevos datos.
            </em>
          </b-alert>

          <!-- ALERT SUCCESS-->
          <div class="col-12 text-center mx-auto my-4">
            <ul>
              <b-alert
                :show="dismissCountDown2"
                variant="success"
                @dismissed="dismissCountDown2=0"
                @dismiss-count-down="countDownChanged2"
              >
                <b>{{correcto2}} {{ dismissCountDown2 }} segundos...</b>
              </b-alert>
            </ul>
          </div>
          <!-- ALERT SUCCESS -->
        </b-card>
      </div>

      <div class="col-12 col-lg-12">
        <b-card class="text-center tituloTabla transparencia mb-4">
          <b-card-header class="fondoCategoria mb-4">PERFIL</b-card-header>

          <div class="row">
            <div class="col-12 col-md-3 col-lg-3 text-left">
              <b>NOMBRE DE USUARIO:</b>
            </div>

            <div class="col-8 col-md-6 col-lg-6 mb-4">
              <b-form-input placeholder="Cambiar nombre de usuario" v-model="nameUpd"></b-form-input>
            </div>

            <div class="col-4 col-md-3 col-lg-3">
              <b-button block variant="success" @click="actualizar_dato('name',nameUpd)">
                <i class="fas fa-edit"></i>
              </b-button>
            </div>

            <div class="col-12 col-md-3 col-lg-3 text-left">
              <b>CORREO ELECTRONICO:</b>
            </div>

            <div class="col-8 col-md-6 col-lg-6 mb-4">
              <b-form-input placeholder="Cambiar correo electronico" v-model="emailUpd"></b-form-input>
            </div>

            <div class="col-4 col-md-3 col-lg-3">
              <b-button block variant="success" @click="actualizar_dato('email',emailUpd)">
                <i class="fas fa-edit"></i>
              </b-button>
            </div>

            <!-- ALERT ERRROR -->
            <div class="col-12 col-lg-8 text-center mx-auto">
              <ul v-for="e2 in errores2" :key="e2[0]">
                <b-alert variant="danger" show>
                  <li>{{e2[0]}}</li>
                </b-alert>
              </ul>
            </div>
            <!-- ALERT ERROR -->
          </div>
        </b-card>
      </div>

      <div class="col-12 col-lg-12">
        <b-card class="text-center tituloTabla transparencia">
          <b-card-header class="fondoCategoria mb-4">CAMBIO DE CONTRASEÑA</b-card-header>

          <div class="row">
            <div class="col-12 col-md-3 col-lg-3 text-left">
              <b>CONTRASEÑA ACTUAL:</b>
            </div>

            <div class="col-12 col-md-9 col-lg-9 mb-4">
              <b-form-input v-model="passActual" type="password"></b-form-input>
            </div>

            <div class="col-12 col-md-3 col-lg-3 text-left">
              <b>NUEVA CONTRASEÑA:</b>
            </div>

            <div class="col-12 col-md-9 col-lg-9 mb-4">
              <b-form-input v-model="passNueva" type="password"></b-form-input>
            </div>

            <div class="col-12 col-md-3 col-lg-3 text-left">
              <b>REPETIR CONTRASEÑA:</b>
            </div>

            <div class="col-12 col-md-9 col-lg-9 mb-4">
              <b-form-input v-model="passRepetir" type="password"></b-form-input>
            </div>
          </div>

          <div class="row justify-content-center">
            <div class="col-12 col-lg-4">
              <b-button block variant="success" @click="actualizar_password()">
                <i class="fas fa-edit"></i>
              </b-button>
            </div>
          </div>
        </b-card>
      </div>
    </b-container>
  </div>
</template>

<script src="../perfil/perfil.js"></script>
<style scoped src="../perfil/perfil.css"></style>
