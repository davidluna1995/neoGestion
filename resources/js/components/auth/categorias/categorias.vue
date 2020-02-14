<template>
  <div>
    <div class="row m-4">
      <!-- formulario de categorias -->
      <div class="col-12 col-md-12 col-lg-4">
        <b-card class="text-center tituloTabla my-2 transparencia">
          <b-card-header class="fondoCategoria mb-4">AGREGAR CATEGORIA</b-card-header>

          <div class="row">
            <div class="col-12">
              <b-form-input type="text" v-model="categoria" placeholder="Nombre de la categoria"></b-form-input>
            </div>
          </div>

          <div class="row justify-content-center">
            <div class="col-10">
              <b-button class="my-4" block pill variant="success" @click="registrar_categorias()">
                <b>Guardar</b>
              </b-button>
            </div>
          </div>
        </b-card>
        <!-- formulario de categorias -->

        <!-- alertas -->
        <div class="col-12 my-4">
          <ul v-for="e in errores" :key="e[0]">
            <b-alert variant="danger" show>
              <li>{{e[0]}}</li>
            </b-alert>
          </ul>
        </div>

        <div class="col-12 my-4">
          <ul>
            <b-alert
              :show="dismissCountDown"
              variant="success"
              @dismissed="dismissCountDown=0"
              @dismiss-count-down="countDownChanged"
            >{{correcto}} {{ dismissCountDown }} segundos...</b-alert>
          </ul>
        </div>

        <div class="col-12 my-4">
          <ul>
            <b-alert
              :show="dismissCountDown2"
              variant="success"
              @dismissed="dismissCountDown2=0"
              @dismiss-count-down="countDownChanged2"
            >{{correcto2}} {{ dismissCountDown2 }} segundos...</b-alert>
          </ul>
        </div>

        <div class="col-12 my-4">
          <ul>
            <b-alert
              :show="dismissCountDown3"
              variant="warning"
              @dismissed="dismissCountDown3=0"
              @dismiss-count-down="countDownChanged3"
            >{{errores3}} {{ dismissCountDown3 }} segundos...</b-alert>
          </ul>
        </div>
        <!-- alertas -->

        <!--GRAFICO  -->
        <b-card class="text-center tituloTabla my-2 transparencia">
          <b-card-header class="fondoCategoria mb-4">GRAFICO</b-card-header>
        </b-card>
        <!--GRAFICO  -->
      </div>

      <!-- tabla de categorias -->
      <div class="col-12 col-md-12 col-lg-8">
        <b-card class="text-center tituloTabla my-2 transparencia">
          <b-card-header class="fondoCategoria mb-4">LISTA DE CATEGORIAS</b-card-header>

          <div class="row">
            <div class="col-12 col-sm-8 col-md-8 col-lg-8 col-xl-8">
              <b-input-group>
                <b-form-input
                  v-on:keyup="escribiendo"
                  placeholder="Buscar categoria"
                  v-model="buscadorCategoria"
                ></b-form-input>
              </b-input-group>
            </div>
            <div class="col-12 col-sm-2 col-md-2 col-lg-2 col-xl-2 mx-auto mb-4">
              <b-button :disabled="btn_buscar" block variant="success" @click="traer_categoria()">
                <i class="fas fa-search text-light"></i>
              </b-button>
            </div>
            <div class="col-12 col-sm-2 col-md-2 col-lg-2 col-xl-2 mx-auto mb-4">
              <b-button block variant="success" @click="traer_categorias()">
                <i class="fas fa-sync-alt text-light"></i>
              </b-button>
            </div>
          </div>

          <div>
            <b-table
              table
              hove12
              hovexl-r
              bordered
              small
              :fields="productosFields"
              :items="listarCategorias"
              sticky-header="300px"
              head-variant="dark"
              responsive="sm"
            >
              <template v-slot:cell(index)="data">
                <div class="col-12">{{ data.item.id }}</div>
              </template>

              <template v-slot:cell(cat)="data">
                <div class="col-12">{{ data.item.descripcion.toUpperCase() }}</div>
              </template>

              <template v-slot:cell(fecha)="data">
                <div class="col-12">{{ data.item.creado }}</div>
              </template>
              <template v-slot:cell(usuario)="data">
                <div class="col-12">{{ data.item.nombreUsuario }}</div>
              </template>

              <template v-slot:cell(editar)="data">
                <div class="col-12">
                  <b-button
                    size="sm"
                    id="show-btn-editar"
                    @click="showModalCategoria(data.item.id);"
                    pill
                    block
                    class="my-2"
                    variant="success"
                  >Editar</b-button>
                </div>

                <!-- modal categorias -->
                <div>
                  <b-modal
                    class="modal-header-editar"
                    id="modal-lg"
                    size="lg"
                    :ref="'editarModalCategoria'+data.item.id"
                    hide-footer
                    centered
                  >
                    <template v-slot:modal-title>
                      <h5 class="text-center">EDITAR CATEGORIA</h5>
                    </template>
                    <div class="d-block text-center">
                      <div class="row">
                        <div class="col-10 col-xl-5 mb-4">
                          <b-input-group>
                            <i class="fas fa-clipboard-list mr-1 mt-1 fa-2x text-secondary"></i>
                            <b-form-input :value="data.item.descripcion" disabled>
                              <!-- :value="data.item.descripcion" -->
                            </b-form-input>
                          </b-input-group>
                        </div>
                        <div class="col-10 col-xl-5 mb-4">
                          <b-input-group>
                            <i class="fas fa-clipboard-list mr-1 mt-1 fa-2x text-secondary"></i>
                            <b-form-input placeholder="Editar categoria" v-model="campoUpd"></b-form-input>
                          </b-input-group>
                        </div>
                        <div class="col-2 col-xl-2">
                          <b-button
                            block
                            variant="light"
                            @click="actualizar_dato(data.item.id,'descripcion',campoUpd)"
                          >
                            <!-- @click="actualizar_dato(data.item.id,'descripcion',campoUpd)" -->
                            <i class="fas fa-edit text-success"></i>
                          </b-button>
                        </div>

                        <!-- alert modal -->
                        <div class="col-12">
                          <ul v-for="e2 in errores2" :key="e2[0]">
                            <b-alert variant="danger" show>
                              <li>{{e2[0]}}</li>
                            </b-alert>
                          </ul>
                        </div>
                      </div>
                    </div>
                    <div class="row justify-content-center bordeFooter">
                      <div class="col-4">
                        <b-button
                          class="my-2"
                          block
                          pill
                          variant="info"
                          @click="hideModal(data.item.id)"
                        >
                          <!--   @click="hideModal(data.item.id)" -->
                          Volver
                        </b-button>
                      </div>
                    </div>
                  </b-modal>
                </div>
              </template>

              <template v-slot:cell(eliminar)>
                <div class="col-12">
                  <b-button
                    size="sm"
                    id="show-btn-eliminar"
                    pill
                    block
                    class="my-2"
                    variant="danger"
                  >Eliminar</b-button>
                </div>
              </template>
            </b-table>
          </div>
        </b-card>
      </div>
    </div>
  </div>
</template>


<script src="../categorias/categorias.js"></script>
<style src="../categorias/categorias.css"></style>