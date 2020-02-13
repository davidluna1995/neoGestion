<template>
  <div>
    <div class="row justify-content-center m-4">
      <div class="col-12 col-md-12">
        <b-card class="text-center tituloTabla transparencia">
          <b-card-header class="fondoProductoAdm mb-4">LISTA DE PRODUCTOS</b-card-header>

          <div class="row">
            <div class="col-12 col-sm-8 col-md-8 col-lg-8 col-xl-8">
              <b-input-group>
                <b-form-input
                  id="inputBuscar"
                  v-on:keyup="escribiendoProducto"
                  placeholder="Buscar producto"
                  v-model="buscadorProducto"
                ></b-form-input>
              </b-input-group>
            </div>
            <div class="col-12 col-sm-2 col-md-2 col-lg-2 col-xl-2 mx-auto mb-4">
              <b-button
              id="btnBuscar"
                :disabled="btn_buscar_producto"
                block
                variant="success"
                @click="traer_producto()"
              >
                <i class="fas fa-search text-light"></i>
              </b-button>
            </div>
            <div class="col-12 col-sm-2 col-md-2 col-lg-2 col-xl-2 mx-auto mb-4">
              <b-button block variant="success" @click="traer_productos()">
                <i class="fas fa-sync-alt text-light"></i>
              </b-button>
            </div>
            <!-- SUCCESS EDITAR -->
            <div class="col-12">
              <ul>
                <b-alert
                  variant="success"
                  :show="dismissCountDown2"
                  @dismissed="dismissCountDown2=0"
                  @dismiss-count-down="countDownChanged2"
                >
                  <p>{{correcto2}} {{ dismissCountDown2 }} segundos...</p>
                  <b-progress
                    variant="success"
                    height="4px"
                    :max="dismissSecs2"
                    :value="dismissCountDown2"
                  ></b-progress>
                </b-alert>
              </ul>
            </div>
            <!-- SUCCESS VENTA -->
            <div class="col-12">
              <ul>
                <b-alert
                  variant="success"
                  :show="dismissCountDown3"
                  @dismissed="dismissCountDown3=0"
                  @dismiss-count-down="countDownChanged3"
                >
                  <p>{{correcto3}} {{ dismissCountDown3 }} segundos...</p>
                  <b-progress
                    variant="success"
                    height="4px"
                    :max="dismissSecs3"
                    :value="dismissCountDown3"
                  ></b-progress>
                </b-alert>
              </ul>
            </div>
            <!-- BUSQUEDA ERRONEA -->
            <div class="col-12">
              <ul>
                <!-- SUCCESS BUSQUETA -->
                <b-alert
                  variant="danger"
                  :show="dismissCountDown4"
                  @dismissed="dismissCountDown4=0"
                  @dismiss-count-down="countDownChanged4"
                >
                  <p>{{errores4}} {{ dismissCountDown4 }} segundos...</p>
                  <b-progress
                    variant="danger"
                    height="4px"
                    :max="dismissSecs4"
                    :value="dismissCountDown4"
                  ></b-progress>
                </b-alert>
              </ul>
            </div>
            <!-- BUSCADOR VACIO -->
            <div class="col-12">
              <b-alert v-model="showAlertBuscar" variant="danger" dismissible>{{errorBuscar}}</b-alert>
            </div>
          </div>

          <div>
            <b-table
              table
              hover
              bordered
              responsive
              small
              striped
              no-border-collapse
              :fields="productosFieldsAdm"
              :items="listarProductos"
              sticky-header="400px"
              head-variant="dark"
                   primary-key="a"
      :tbody-transition-props="transProps"
        id="table-transition-example">

              <template v-slot:cell(index)="data">
                <div class="col-12">{{ data.item.id }}</div>
              </template>

              <template v-slot:cell(prod)="data">
                <div class="col-12">{{ data.item.nombre }}</div>
              </template>
              <template v-slot:cell(cat)="data">
                <div class="col-12">{{ data.item.catDesc }}</div>
              </template>
              <template v-slot:cell(desc)="data">
                <div class="col-12">{{ data.item.proDesc }}</div>
              </template>
              <template v-slot:cell(stock)="data">
                <div class="col-12">{{formatPrice(data.item.cantidad)}}</div>
              </template>
              <template v-slot:cell(compra)="data">
                <div class="col-12">
                  <span class="green">$</span>
                  {{ formatPrice(data.item.precio_compra) }}
                </div>
              </template>
              <template v-slot:cell(venta)="data">
                <div class="col-12">
                  <span class="green">$</span>
                  {{ formatPrice(data.item.precio_venta) }}
                </div>
              </template>
              <template v-slot:cell(fecha)="data">
                <div class="col-12">{{data.item.creado}} hrs.</div>
              </template>

              <template v-slot:cell(editar)="data">
                <!-- EDITAR PRODUCTOS -->
                <div class="col-12 col-xl-12">
                  <!-- BOTON EDITAR -->
                  <div>
                    <b-button
                      pill
                      block
                      size="sm"
                      id="show-btn"
                      class="my-2"
                      variant="success"
                      @click="showModal2(data.item.id)"
                    >Editar</b-button>
                  </div>
                  <!-- MODAL EDITAR PRODUCTOS -->
                  <template>
                    <div>
                      <b-modal
                        hide-footer
                        centered
                        class="modal-header-editar"
                        id="modal-md"
                        size="md"
                        :ref="'editarModal'+data.item.id"
                      >
                        <template v-slot:modal-title>
                          <h5 class="text-center">EDITAR PRODUCTO</h5>
                        </template>
                        <div class="d-block text-center">
                          <div class="row">
                            <div class="col-8 mb-4">
                              <div class="row">
                                <div class="col-2">
                                  <i class="fas fa-clipboard-list mr-1 mt-1 fa-2x text-secondary"></i>
                                </div>
                                <div class="col-10">
                                  <b-form-input v-model="nombreUpd" :placeholder="data.item.nombre"></b-form-input>
                                </div>
                              </div>
                            </div>
                            <div class="col-3">
                              <b-button
                                block
                                variant="light"
                                @click="actualizar_dato(data.item.id,'nombre',nombreUpd)"
                              >
                                <i class="fas fa-edit text-success"></i>
                              </b-button>
                            </div>

                            <div class="col-8 mb-4">
                              <b-form-group>
                                <div class="row">
                                  <div class="col-2">
                                    <i class="fas fa-list-ul mr-1 mt-1 fa-2x colorCategoria"></i>
                                  </div>
                                  <div class="col-10">
                                    <multiselect
                                      v-model="categoria_id"
                                      :options="listarCategorias"
                                      :custom-label="buscadorCategorias"
                                      open-direction="bottom"
                                      :placeholder="data.item.catDesc"
                                      select-label
                                      selectedLabel="Selecionado"
                                      deselectLabel="Quitar"
                                    >
                                      <span slot="noResult">No se encontraron resultados.</span>
                                    </multiselect>
                                  </div>
                                </div>
                              </b-form-group>
                            </div>

                            <div class="col-3">
                              <b-button
                                block
                                variant="light"
                                @click="actualizar_dato(data.item.id,'categoria_id',categoria_id.id)"
                              >
                                <i class="fas fa-edit text-success"></i>
                              </b-button>
                            </div>

                            <div class="col-8 mb-4">
                              <div class="row">
                                <div class="col-2">
                                  <i class="fas fa-file-signature mr-1 mt-1 fa-2x text-secondary"></i>
                                </div>
                                <div class="col-10">
                                  <b-form-input
                                    v-model="descripcionUpd"
                                    :placeholder="data.item.proDesc"
                                  ></b-form-input>
                                </div>
                              </div>
                            </div>
                            <div class="col-3">
                              <b-button
                                block
                                variant="light"
                                @click="actualizar_dato(data.item.id,'descripcion',descripcionUpd)"
                              >
                                <i class="fas fa-edit text-success"></i>
                              </b-button>
                            </div>

                            <div class="col-8 mb-4">
                              <div class="row">
                                <div class="col-2">
                                  <i class="fas fa-sort-numeric-up-alt mr-1 mt-1 fa-2x"></i>
                                </div>
                                <div class="col-10">
                                  <b-form-input
                                    v-model="cantidadUpd"
                                    :placeholder="formatPrice(data.item.cantidad)"
                                  ></b-form-input>
                                </div>
                              </div>
                            </div>
                            <div class="col-3">
                              <b-button
                                block
                                variant="light"
                                @click="actualizar_dato(data.item.id,'cantidad',cantidadUpd)"
                              >
                                <i class="fas fa-edit text-success"></i>
                              </b-button>
                            </div>

                            <div class="col-8 mb-4">
                              <div class="row">
                                <div class="col-2">
                                  <i class="fas fa-dollar-sign mr-1 mt-1 fa-2x text-primary"></i>
                                </div>
                                <div class="col-10">
                                  <b-form-input
                                    v-model="precioCompraUpd"
                                    :placeholder="formatPrice(data.item.precio_compra)"
                                  ></b-form-input>
                                </div>
                              </div>
                            </div>
                            <div class="col-3">
                              <b-button
                                block
                                variant="light"
                                @click="actualizar_dato(data.item.id,'precio_compra',precioCompraUpd)"
                              >
                                <i class="fas fa-edit text-success"></i>
                              </b-button>
                            </div>

                            <div class="col-8 mb-4">
                              <div class="row">
                                <div class="col-2">
                                  <i class="fas fa-dollar-sign mr-1 mt-1 fa-2x text-success"></i>
                                </div>
                                <div class="col-10">
                                  <b-form-input
                                    v-model="precioVentaUpd"
                                    :placeholder="formatPrice(data.item.precio_venta)"
                                  ></b-form-input>
                                </div>
                              </div>
                            </div>
                            <div class="col-3">
                              <b-button
                                block
                                variant="light"
                                @click="actualizar_dato(data.item.id,'precio_venta',precioVentaUpd)"
                              >
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
                              @click="hideModal2(data.item.id)"
                            >Volver</b-button>
                          </div>
                        </div>
                      </b-modal>
                    </div>
                  </template>
                </div>
              </template>

              <template v-slot:cell(ventaModal)="data">
                <div class="col-12">
                  <!-- VENTAS -->
                  <div class="col-12 col-xl-12">
                    <div>
                      <!-- BOTON VENTAS -->
                      <div>
                        <b-button
                          pill
                          block
                          size="sm"
                          id="show-btn"
                          class="my-2"
                          variant="info"
                          @click="showModal(data.item.id);"
                        >Venta</b-button>
                      </div>
                      <!-- MODAL VENTAS  -->
                      <template>
                        <div>
                          <b-modal
                            class="modal-header-ventas"
                            id="modal-sm"
                            size="sm"
                            :ref="'ventasModal'+data.item.id"
                            hide-footer
                            centered
                          >
                            <template v-slot:modal-title>
                              <h5 class="text-center">VENTAS DEL DIA</h5>
                            </template>
                            <div class="d-block text-center">
                              <div class="row">
                                <div class="col-12 mb-4">
                                  <div class="row">
                                    <div class="col-2">
                                      <i
                                        class="fas fa-clipboard-list mr-1 mt-1 fa-2x text-secondary"
                                      ></i>
                                    </div>
                                    <div class="col-10">
                                      <b-form-input
                                        :v-model="producto_id"
                                        :value="data.item.nombre"
                                        disabled
                                      ></b-form-input>
                                    </div>
                                  </div>
                                </div>

                                <div class="col-12 mb-4">
                                  <div class="row">
                                    <div class="col-2">
                                      <i
                                        class="fas fa-clipboard-list mr-1 mt-1 fa-2x text-secondary"
                                      ></i>
                                    </div>
                                    <div class="col-10">
                                      <b-form-input
                                        :value="data.item.precio_venta + ' c/u'"
                                        disabled
                                      ></b-form-input>
                                    </div>
                                  </div>
                                </div>

                                <div class="col-12 mb-4">
                                  <div class="row">
                                    <div class="col-2">
                                      <i class="fas fa-sort-numeric-up-alt mr-1 mt-1 fa-2x"></i>
                                    </div>
                                    <div class="col-10">
                                      <b-form-input
                                        v-model="cantidad"
                                        placeholder="Cantidad vendida"
                                      ></b-form-input>
                                    </div>
                                  </div>
                                </div>

                                <div class="col-12 mb-4">
                                  <div class="row">
                                    <div class="col-2">
                                      <i class="fas fa-dollar-sign mr-1 mt-1 fa-2x text-success"></i>
                                    </div>
                                    <div class="col-10">
                                      <b-form-input
                                        :v-model="ventas=(cantidad * data.item.precio_venta)"
                                        :value="ventas=(cantidad * data.item.precio_venta)"
                                        disabled
                                      ></b-form-input>
                                    </div>
                                  </div>
                                </div>

                                <!-- alert modal -->
                                <div class="col-12">
                                  <b-alert
                                    v-model="showAlertStock"
                                    variant="danger"
                                    dismissible
                                  >{{errorStock}}</b-alert>
                                </div>

                                <div class="col-12">
                                  <ul v-for="e3 in errores3" :key="e3[0]">
                                    <b-alert variant="danger" show>
                                      <li>{{e3[0]}}</li>
                                    </b-alert>
                                  </ul>
                                </div>
                              </div>
                            </div>
                            <div class="row justify-content-center bordeFooter">
                              <div class="col-6">
                                <b-button
                                  class="my-2"
                                  block
                                  pill
                                  variant="success"
                                  @click="registrar_venta();"
                                >Ingresar</b-button>
                              </div>
                              <div class="col-6">
                                <b-button
                                  id="volverVenta"
                                  class="my-2"
                                  block
                                  pill
                                  variant="info"
                                  @click="hideModal(data.item.id)"
                                >Volver</b-button>
                              </div>
                            </div>
                          </b-modal>
                        </div>
                      </template>
                    </div>
                  </div>
                </div>
              </template>

              <template v-slot:cell(eliminarProd)>
                <div class="col-12">
                  <b-button size="sm" pill block class="my-2" variant="danger">Borrar</b-button>
                </div>
              </template>
            </b-table>
          </div>
        </b-card>
      </div>
    </div>
  </div>
</template>

<script src="../administrarProducto/administrarProducto.js"></script>
<style scoped src="../administrarProducto/administrarProducto.css"></style>
<style src="vue-multiselect/dist/vue-multiselect.min.css"></style>
