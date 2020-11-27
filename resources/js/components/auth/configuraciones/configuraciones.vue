<template>
  <div class="mt-4">
    <b-container class="bv-example-row">
      <div class="col-12 col-lg-12">
        <b-card class="text-center tituloTabla transparencia mb-4">
          <b-card-header class="fondoCategoria mb-4">CONFIGURACIONES</b-card-header>

          <center><h5>GESTION GENERAL <i class="fas fa-user-cog"></i></h5></center>
          <div class="row">
            <div class="col-6">
              <!-- <b-button
                size="xl"
                id="show-btn"
                class="my-2"
                variant="success"
                @click="showModalCrearUsuario()"
              >CREAR USUARIO</b-button> -->

               <b-button
                size="xl"
                id="show-btn"
                class="my-2"
                variant="success"
                @click="showModalBloquearUsuario()"
              >BLOQUEAR USUARIO</b-button>

              <template>
                <div>
                  <b-modal
                    hide-footer
                    centered
                    class="modal-header-editar"
                    id="modal-xl"
                    size="xl"
                    ref="crearUsuario"
                  >
                    <template v-slot:modal-title>
                      <h5 class="text-center">CREAR USUARIO</h5>
                    </template>

                    <div class="row">
                      <div class="col-12 col-md-3 col-lg-3 text-left">
                        <b>NOMBRE DE USUARIO:</b>
                      </div>

                      <div class="col-12 col-md-9 col-lg-9 mb-4">
                        <b-form-input v-model="nombre" type="text"></b-form-input>
                      </div>

                      <div class="col-12 col-md-3 col-lg-3 text-left">
                        <b>CORREO ELECTRONICO:</b>
                      </div>

                      <div class="col-12 col-md-9 col-lg-9 mb-4">
                        <b-form-input v-model="email" type="email"></b-form-input>
                      </div>

                      <div class="col-12 col-md-3 col-lg-3 text-left">
                        <b>TIPO DE USUARIO :</b>
                      </div>

                      <div class="col-12 col-md-9 col-lg-9 mb-4">
                        <b-form-select v-model="selectedRol" :options="optionsRol"></b-form-select>
                      </div>

                      <div class="col-12 col-md-3 col-lg-3 text-left">
                        <b>CONTRASEÑA:</b>
                      </div>

                      <div class="col-12 col-md-9 col-lg-9 mb-4">
                        <b-form-input v-model="password" type="password"></b-form-input>
                      </div>

                      <div class="col-12 col-md-3 col-lg-3 text-left">
                        <b>REPETIR CONTRASEÑA:</b>
                      </div>

                      <div class="col-12 col-md-9 col-lg-9 mb-4">
                        <b-form-input v-model="passRepetir" type="password"></b-form-input>
                      </div>
                    </div>

                    <div class="row justify-content-center bordeFooter">
                      <div class="col-12 col-lg-4">
                        <b-button
                          class="mt-2"
                          pill
                          block
                          variant="success"
                          @click="crear_usuario()"
                        >Crear</b-button>
                      </div>
                      <div class="col-12 col-lg-4">
                        <b-button
                          class="mt-2"
                          block
                          pill
                          variant="info"
                          @click="hideModalCrearUsuario()"
                        >Volver</b-button>
                      </div>

                      <!-- ALERT SUCCESS-->
                      <div class="col-12 text-center mx-auto my-4">
                        <ul>
                          <b-alert
                            :show="dismissCountDown2"
                            variant="success"
                            @dismissed="dismissCountDown2=0"
                            @dismiss-count-down="contadorUsuarioCreado"
                          >
                            <b>{{correcto2}} {{ dismissCountDown2 }} segundos...</b>
                          </b-alert>
                        </ul>
                      </div>

                      <div class="col-12">
                        <ul v-for="e in errores" :key="e[0]">
                          <b-alert variant="danger" show>
                            <li>
                              <b>{{e[0]}}</b>
                            </li>
                          </b-alert>
                        </ul>
                      </div>

                      <div class="col-12 text-center mx-auto">
                        <ul>
                          <b-alert
                            dismissible
                            fade
                            :show="countPassword"
                            @dismissed="countPassword=false"
                            variant="warning"
                          >
                            <li>
                              <b>{{errorPassword}}</b>
                            </li>
                          </b-alert>
                        </ul>
                      </div>

                      <!-- ALERT SUCCESS -->
                    </div>
                  </b-modal>
                </div>
              </template>
            </div>

            <div class="col-6">
              <!-- <b-button
                size="xl"
                id="show-btn"
                class="my-2"
                variant="success"
                @click="showModalBloquearUsuario()"
              >BLOQUEAR USUARIO</b-button> -->

              <b-button
                size="xl"
                id="show-btn"
                class="my-2"
                variant="success"
                @click="showModalConf()"
              >INFORMACION DE LA EMPRESA</b-button>

              <template>
                <div>
                  <b-modal
                    hide-footer
                    centered
                    class="modal-header-editar"
                    id="modal-xl"
                    size="xl"
                    ref="bloquearUsuario"
                  >
                    <template v-slot:modal-title>
                      <h5 class="text-center">BLOQUEAR USUARIO</h5>
                    </template>

                    <b-table
                      show-empty
                      emptyText="Cargando usuarios..."
                      small
                      striped
                      hover
                      bordered
                      stacked="lg"
                      head-variant="dark"
                      :fields="usuariosFields"
                      :items="listarUsuarios"
                    >
                      <template v-slot:cell(name)="data">{{ data.item.name }}</template>
                      <template v-slot:cell(email)="data">{{ data.item.email }}</template>
                      <template v-slot:cell(creado)="data">{{ data.item.created_at }}</template>
                      <template v-slot:cell(bloquear)="data">
                        <b-button
                          v-if="!data.item.deleted_at"
                          variant="success"
                          @click="bloquear_usuario(true,data.item.id)"
                        >Bloquear</b-button>
                        <b-button
                          v-if="data.item.deleted_at"
                          variant="danger"
                          @click="bloquear_usuario(false,data.item.id)"
                        >Desbloquear</b-button>
                      </template>
                    </b-table>

                    <!-- ALERT SUCCESS-->
                    <div class="col-12 text-center mx-auto my-4">
                      <ul>
                        <b-alert
                          :show="dismissCountDown3"
                          variant="success"
                          @dismissed="dismissCountDown3=0"
                          @dismiss-count-down="contadorRolUsuario"
                        >
                          <b>{{correcto3}} {{ dismissCountDown3 }} segundos...</b>
                        </b-alert>
                      </ul>
                    </div>

                    <div class="col-12 text-center mx-auto">
                      <ul>
                        <b-alert
                          dismissible
                          fade
                          :show="countBloquear"
                          @dismissed="countBloquear=false"
                          variant="warning"
                        >
                          <li>
                            <b>{{errorBloquear}}</b>
                          </li>
                        </b-alert>
                      </ul>
                    </div>

                    <!-- ALERT SUCCESS -->

                    <div class="row justify-content-center bordeFooter">
                      <div class="col-4">
                        <b-button
                          class="my-2"
                          block
                          pill
                          variant="info"
                          @click="hideModalBloquearUsuario()"
                        >Volver</b-button>
                      </div>
                    </div>
                  </b-modal>
                </div>
              </template>
            </div>

            <div class="col-6">
              <!-- <b-button
                size="xl"
                id="show-btn"
                class="my-2"
                variant="success"
                @click="showModalConf()"
              >INFORMACION DE LA EMPRESA</b-button> -->

              <template>
                <div>
                  <b-modal
                    hide-footer
                    centered
                    class="modal-header-editar"
                    id="modal-xl"
                    size="xl"
                    ref="configuraciones"
                  >
                    <template v-slot:modal-title>
                      <h5 class="text-center">
                        INFORMACION DE LA EMPRESA
                        <img
                          :src="listarConf.logo"
                          v-show="logoNull"
                          width="80px"
                          height="50px"
                        />
                      </h5>
                    </template>

                    <div class="row">
                      <b-alert show variant="warning">
                        <b>NOTA:</b>
                        <em>
                          Al cambiar algun dato de este formulario por seguridad la
                          sesión se cerrará automaticamente para que efectue un nuevo
                          inicio de sesión con los nuevos datos ingresados.
                        </em>
                      </b-alert>
                      <div class="col-12 col-md-3 col-lg-3 text-left">
                        <b>SUBIR LOGO:</b>
                      </div>

                      <div class="col-12 col-md-9 col-lg-9 mb-4">
                        <b-form-file
                          @change="onFileChange"
                          placeholder="Seleccione un archivo..."
                          browse-text="Elegir"
                        ></b-form-file>
                      </div>

                      <div class="col-12 col-md-3 col-lg-3 text-left">
                        <b>NOMBRE DE LA EMPRESA (RAZON SOCIAL):</b>
                      </div>

                      <div class="col-12 col-md-9 col-lg-9 mb-4">
                        <b-form-input v-model="empresa" type="text"></b-form-input>
                      </div>

                      <div class="col-12 col-md-3 col-lg-3 text-left">
                        <b>DIRECCION DE LA EMPRESA:</b>
                      </div>

                      <div class="col-12 col-md-9 col-lg-9 mb-4">
                        <b-form-input v-model="direccion" type="text"></b-form-input>
                      </div>
                    </div>

                    <div class="row justify-content-center bordeFooter">
                      <div class="col-12 col-lg-4">
                        <b-button
                          class="mt-2"
                          pill
                          block
                          variant="success"
                          @click="crear_actualizar_configuraciones()"
                        >Crear / Actualizar</b-button>
                      </div>
                      <div class="col-12 col-lg-4">
                        <b-button
                          class="mt-2"
                          block
                          pill
                          variant="info"
                          @click="hideModalConf()"
                        >Volver</b-button>
                      </div>

                      <!-- ALERT SUCCESS-->
                      <div class="col-12 text-center mx-auto my-4">
                        <ul>
                          <b-alert
                            :show="dismissCountDown"
                            variant="success"
                            @dismissed="dismissCountDown=0"
                            @dismiss-count-down="contadorConf"
                          >
                            <b>{{correcto}} {{ dismissCountDown }} segundos...</b>
                          </b-alert>
                        </ul>
                      </div>

                      <div class="col-12">
                        <ul v-for="e in erroresConf" :key="e[0]">
                          <b-alert variant="danger" show>
                            <li>
                              <b>{{e[0]}}</b>
                            </li>
                          </b-alert>
                        </ul>
                      </div>

                      <div class="col-12 text-center mx-auto">
                        <ul>
                          <b-alert
                            dismissible
                            fade
                            :show="countPassword"
                            @dismissed="countPassword=false"
                            variant="warning"
                          >
                            <li>
                              <b>{{errorPassword}}</b>
                            </li>
                          </b-alert>
                        </ul>
                      </div>

                      <!-- ALERT SUCCESS -->
                    </div>
                  </b-modal>
                </div>
              </template>
            </div>
          </div>
          <hr>

          <center><h5>GESTION DE CAJA <i class="fas fa-cash-register"></i></h5></center>

          <div class="row">
            <div class="col-4">
              <b-button
                size="xl"
                id="show-btn"
                class="my-2"
                variant="success"
                @click="abrir_modal('modal_caja')"
              >Ingresar Caja</b-button>



              <b-modal
                    no-close-on-esc
                    no-close-on-backdrop
                    hide-footer
                    centered
                    class="modal-header-editar"
                    id="modal-xl"
                    size="md"
                    ref="modal_caja"
                  >
                  <template v-slot:modal-title>
                      <h5 class="text-center">Ingresar caja <i class="fas fa-cash-register"></i></h5>
                  </template>

                  <div>
                    <label for="">Nombre de caja (Ej:"Caja uno, caja 1"):</label><br>
                    <input v-model="nombre_caja" type="text" class="form-control" placeholder="Nombre de caja..">
                    <br>
                    <label for="">Descripcion de caja: (Ej: "Computadora marca lenovo para uso de caja..")</label>
                    <textarea v-model="detalle_caja" style="rezise:none" placeholder="Descripción de caja.." class="form-control" name="" id="" cols="30" rows="10"></textarea>
                    <br>
                    <button  :disabled="conf_ngreso_caja" @click="ingresar_caja" class="btn btn-success"><i class="far fa-save"></i> Ingresar</button>
                  </div>
                  </b-modal>
            </div>


            <div class="col-4">
              <b-button
                size="xl"
                id="show-btn"
                class="my-2"
                variant="success"
                @click="listar_cajas();abrir_modal('modal_listar_caja')"
              >Listar Caja</b-button>



              <b-modal
                    no-close-on-esc
                    no-close-on-backdrop
                    hide-footer
                    centered
                    class="modal-header-editar"
                    id="modal-xl"
                    size="xl"
                    ref="modal_listar_caja"
                  >
                  <template v-slot:modal-title>
                      <h5 class="text-center">Listar cajas <i class="fas fa-cash-register"></i></h5>
                  </template>

                  <div>
                   <table class="table table-bordered">
                       <tr>
                           <td><b>Nombre</b></td>
                           <td><b>Descripción</b></td>
                           <td><b>Usuario creador</b></td>
                           <td><b>Estado</b></td>
                           <td><b>Usuario activo</b></td>
                           <td colspan="2"><b>Opción</b></td>
                       </tr>

                       <tr v-for="c in get_cajas" :key="c.id">
                           <td><i class="fas fa-cash-register"></i> {{ c.nombre }}</td>
                           <td>{{ c.descripcion }}</td>
                           <td>{{ c.user_crea}}</td>
                           <td v-if="c.activo=='INACTIVA'" style="color:red">{{ c.activo}}</td>
                           <td v-if="c.activo=='ACTIVA'" style="color:green">{{ c.activo}}</td>
                           <td>{{c.user_activo}}</td>
                           <td>
                               <button @click="ver_usuarios_en_caja(c)" class="btn btn-link btn-sm">Ver usuarios en esta caja</button>
                           </td>
                           <td>
                               <button @click="editar_caja(c)" class="btn btn-link btn-sm">Editar</button>
                           </td>
                       </tr>
                   </table>
                  </div>
                  </b-modal>


                <!-- editar -->
                <b-modal
                    no-close-on-esc
                    no-close-on-backdrop
                    hide-footer
                    centered
                    class="modal-header-editar"
                    id="modal-xl"
                    size="md"
                    ref="modal_editar_caja"
                  >
                  <template v-slot:modal-title>
                      <h5 class="text-center">Editar caja <i class="fas fa-cash-register"></i></h5>
                  </template>

                    <div>
                        <!-- <pre>{{ get_editar_caja  }}</pre> -->
                        <label for="">Nombre:</label>
                        <input class="form-control" v-model="caja_edit_nombre" type="text"><br>
                        <label for="">Descripción:</label>
                        <textarea style="resize:none" class="form-control" v-model="caja_edit_descripcion" name="" id="" cols="30" rows="10"></textarea>
                        <!-- <textarea  style="resize:none" class="form-control" v-model="caja_edit_descripcion"></textarea> -->
                        <br>
                        <button @click="editar_datos_caja(get_editar_caja.id)" class="btn btn-success">Actualizar</button>

                    </div>

                </b-modal>
                <!-- fin editar -->


                <!-- ver_usuarios en caja -->
                <b-modal
                    no-close-on-esc
                    no-close-on-backdrop
                    hide-footer
                    centered
                    class="modal-header-editar"
                    id="modal-xl"
                    size="md"
                    ref="modal_usuarios_en_caja"
                  >
                  <template v-slot:modal-title>
                      <h5 class="text-center"><i class="fas fa-cash-register"></i> Ver usuarios en {{datos_de_caja.nombre}}</h5>
                  </template>

                    <div>
                       <!-- ola que hace mijo
                       <pre>{{ usuarios_en_caja }}</pre> -->

                       <table class="table table-bordered">
                           <tr>
                               <td>Nombre</td>
                               <td>Opción</td>
                           </tr>

                           <tr v-for="u in usuarios_en_caja" :key="u.id">
                               <td><i class="fas fa-user"></i> {{ u.name }}</td>
                               <td>
                                   <button class="btn btn-link btn-danger" style="color:white">X</button>
                               </td>
                           </tr>
                       </table>

                    </div>

                </b-modal>
                <!-- ver_usuarios en caja -->

            </div>



            <div class="col-4">
                 <b-button
                size="xl"
                id="show-btn"
                class="my-2"
                variant="success"

                @click="listar_cajas();traer_usuarios();abrir_modal('modal_asignar_caja')"
              >Asignar Caja</b-button>


              <b-modal
                    no-close-on-esc
                    no-close-on-backdrop
                    hide-footer
                    centered
                    class="modal-header-editar"
                    id="modal-xl"
                    size="sm"
                    ref="modal_asignar_caja"
                  >
                  <template v-slot:modal-title>
                      <h5 class="text-center">Asignar caja <i class="fas fa-cash-register"></i></h5>
                  </template>

                  <div>
                      <label for=""><i class="fas fa-cash-register"></i> Cajas disponibles:</label>
                      <select v-model="asig_caja" class="form-control" name="" id="">
                          <option value="">--Seleccione--</option>
                          <option v-for="c in get_cajas" :key="c.id" :value="c.id">{{c.nombre}}</option>
                      </select>
                      <hr>
                      <label for=""><i class="fas fa-user"></i> Usuarios:</label>
                        <select v-model="asig_usuario" class="form-control" name="" id="">
                           <option value="">--Seleccione--</option>
                           <option v-for="c in get_usuarios" :key="c.id" :value="c.id">{{c.name}}</option>
                        </select>
                        <br>
                        <button :disabled="btn_asignar" @click="asignar_usuario_a_caja" class="btn btn-block btn-success">Asignar</button>

                   <!-- <table class="table table-bordered">
                       <tr>
                           <td><b>Nombre</b></td>
                           <td><b>Descripción</b></td>
                           <td><b>Usuario creador</b></td>
                           <td><b>Estado</b></td>
                           <td><b>Opción</b></td>
                       </tr>

                       <tr v-for="c in get_cajas" :key="c.id">
                           <td><i class="fas fa-cash-register"></i> {{ c.nombre }}</td>
                           <td>{{ c.descripcion }}</td>
                           <td>{{ c.name}}</td>
                           <td v-if="c.activo=='Inactiva'" style="color:red">{{ c.activo}}</td>
                           <td v-if="c.activo=='Activa'" style="color:green">{{ c.activo}}</td>
                           <td>
                               <button @click="editar_caja(c)" class="btn btn-link btn-sm">Editar</button>
                           </td>
                       </tr>
                   </table> -->
                  </div>
                  </b-modal>
            </div>
          </div>
        </b-card>
      </div>
    </b-container>
  </div>
</template>

<script src="../configuraciones/configuraciones.js"></script>
<style scoped src="../configuraciones/configuraciones.css"></style>
