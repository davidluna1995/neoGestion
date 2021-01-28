<template>
  <div>
    <div class="row justify-content-center m-2">
      <div class="col-12">
        <b-card class="text-center tituloTabla transparencia">
          <b-card-header class="fondoProductoAdm">LISTA DE PRODUCTOS</b-card-header>

          <b-container class="fondoTotal col-12">
            <div class="row mt-4">
              <!-- ESCRIBIR PRODUCTO -->
              <div class="col-12 col-sm-8 col-md-8 col-lg-8 col-xl-8">
                <b-input-group>
                    <input type="text" id="input_buscador"  @keyup="escribiendoProducto" @keyup.enter="traer_producto()"  placeholder="Buscar producto.." class="form-control my-2">
                  <!-- <b-form-input
                    id="inputBuscar"
                    v-on:keyup="escribiendoProducto"
                    placeholder="Buscar producto..."
                    v-model="buscadorProducto"
                    v-on:keyup.enter="traer_producto()"
                    class="my-2"
                  ></b-form-input> -->
                </b-input-group>
              </div>
              <!-- BUSCAR PRODUCTO -->
              <div class="col-12 col-sm-2 col-md-2 col-lg-2 col-xl-2">
                  <!-- :disabled="btn_buscar_producto" -->
                <b-button
                  id="btnBuscar"

                  block
                  variant="success"
                  class="my-2"
                  @click="traer_producto()"
                >
                  <i class="fas fa-search text-light"></i>
                </b-button>
              </div>
              <!-- REFRESCAR TABLA -->
              <div class="col-12 col-sm-2 col-md-2 col-lg-2 col-xl-2">
                <b-button block variant="success" class="my-2" @click="traer_productos()">
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

            <!-- TABLA DE PRODUCTOS -->
              <div class="table-responsive">
                  <b-pagination
                  pills
                    v-model="currentPage"
                    :total-rows="rows"
                    :per-page="perPage"
                    aria-controls="my-table"
                    ></b-pagination>
                <b-table
                id="my-table"
                :per-page="perPage"
                :current-page="currentPage"
                responsive
                show-empty
                emptyText="No existen productos aun."
                small
                striped
                hover
                bordered
                stacked="lg"
                head-variant="dark"
                :fields="productosFieldsAdm"
                :items="listarProductos"
              >
                <template v-slot:cell(index)="data">
                  <div class="col-12">{{ data.item.sku }}</div>
                </template>
                <template v-slot:cell(imagen)="data">
                  <div class="col-12">

                    <b-img class="tamanio" thumbnail v-if="data.item.imagen"  :src="data.item.imagen" alt="Image 1"></b-img>
                  </div>
                </template>
                <template v-slot:cell(prod)="data">
                  <div class="col-12">{{ data.item.nombre }}</div>
                </template>
                <template v-slot:cell(cat)="data">
                  <div class="col-12">{{ data.item.catdesc }}</div>
                </template>
                <template v-slot:cell(desc)="data">
                  <div class="col-12">{{ data.item.prodesc }}</div>
                </template>
                <template v-slot:cell(stock)="data">
                  <div class="col-12">{{formatPrice(data.item.cantidad)}}</div>
                </template>
                <template v-slot:cell(editar)="data">
                  <!-- EDITAR PRODUCTOS -->
                  <!-- BOTON EDITAR -->
                  <div class="row">
                    <div class="col-12">
                      <b-button
                      v-if="usuario.rol==admin"
                        block
                        id="show-btn"
                        class="my-2"
                        variant="success"
                        @click="showModalEditarProducto(data.item)"
                      >Editar</b-button>
                    </div>
                  </div>

                </template>
                <template v-slot:cell(eliminarProd)="data">
                  <div class="row">
                    <div class="col-12">
                      <b-button v-if="usuario.rol==admin" @click="inhabilitar(data.item.id)" block class="my-2" variant="danger">Inhabilitar </b-button>
                    </div>
                  </div>
                </template>
                <template v-slot:cell(detalle)="data">
                  <div class="row">
                    <div class="col-12">
                      <b-button
                        class="my-2"
                        block
                        @click="data.toggleDetails"
                      >{{ data.detailsShowing ? 'Ocultar' : 'Detalles' }}</b-button>
                    </div>
                  </div>
                </template>
                <template v-slot:row-details="data">
                  <b-card>
                    <b-row class="mb-2">
                      <b-col sm="12" lg="3">
                        <b>Precio 1:</b>
                        <span class="green">$</span>
                        {{ formatPrice(data.item.precio_1) }}  {{(data.item.iva_incluido)?'(IVA incluido)':''}}
                      </b-col>
                      <b-col sm="12" lg="3">
                        <b>Precio 2:</b>
                        <span class="green">$</span>
                        {{ formatPrice(data.item.precio_2) }}
                      </b-col>
                      <b-col sm="12" lg="3">
                        <b>Fecha Creacion:</b>
                        {{ data.item.creado }}
                      </b-col>
                      <b-col sm="12" lg="3">
                        <b>Creado por:</b>
                        {{ data.item.nombreusuario }}
                      </b-col>
                    </b-row>
                  </b-card>
                </template>

              </b-table>
              <b-pagination
                  pills
                    v-model="currentPage"
                    :total-rows="rows"
                    :per-page="perPage"
                    aria-controls="my-table"
                    ></b-pagination>
              </div>
          </b-container>
        </b-card>



        <!-- modal de actualizar productos -->
        <b-modal
                          hide-footer
                          centered
                          class="modal-header-editar"
                          id="modal-md"
                          size="md"
                          :ref="'editarModalProducto'"
                        >
                          <template v-slot:modal-title>
                            <h5 class="text-center">EDITAR PRODUCTO</h5>

                            <!-- <pre>
                                {{item_producto}}
                            </pre> -->
                          </template>
                          <div class="d-block text-center">
                            <div class="row">
                              <div class="col-8 mb-4">
                                <div class="row">
                                  <div class="col-2">
                                    <i class="fas fa-clipboard-list mr-1 mt-1 fa-2x text-secondary"></i>
                                  </div>
                                  <div class="col-10">
                                    <b-form-input v-model="skuUpd" :placeholder="item_producto.sku"></b-form-input>
                                  </div>
                                </div>
                              </div>
                              <div class="col-3">
                                <b-button
                                  block
                                  variant="light"
                                  @click="actualizar_dato(item_producto.id,'sku',skuUpd)"
                                >
                                  <i class="fas fa-edit text-success"></i>
                                </b-button>
                              </div>

                              <div class="col-8 mb-4">
                                <div class="row">
                                  <div class="col-2">
                                    <i class="fas fa-clipboard-list mr-1 mt-1 fa-2x text-secondary"></i>
                                  </div>
                                  <div class="col-10">
                                    <b-form-input v-model="nombreUpd" :placeholder="item_producto.nombre"></b-form-input>
                                  </div>
                                </div>
                              </div>
                              <div class="col-3">
                                <b-button
                                  block
                                  variant="light"
                                  @click="actualizar_dato(item_producto.id,'nombre',nombreUpd)"
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
                                        :placeholder="item_producto.catdesc"
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
                                  @click="actualizar_dato(item_producto.id,'categoria_id',categoria_id.id)"
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
                                      :placeholder="item_producto.prodesc"
                                    ></b-form-input>
                                  </div>
                                </div>
                              </div>
                              <div class="col-3">
                                <b-button
                                  block
                                  variant="light"
                                  @click="actualizar_dato(item_producto.id,'descripcion',descripcionUpd)"
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
                                      :placeholder="formatPrice(item_producto.cantidad)"
                                    ></b-form-input>
                                  </div>
                                </div>
                              </div>
                              <div class="col-3">
                                <b-button
                                  block
                                  variant="light"
                                  @click="actualizar_dato(item_producto.id,'cantidad',cantidadUpd)"
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
                                      v-model="precio_1"
                                      :placeholder="formatPrice(item_producto.precio_1)"
                                    ></b-form-input>
                                  </div>
                                </div>
                              </div>
                              <div class="col-3">
                                <b-button
                                  block
                                  variant="light"
                                  @click="actualizar_dato(item_producto.id,'precio_1',precio_1)"
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
                                      v-model="precio_2"
                                      :placeholder="formatPrice(item_producto.precio_2)"
                                    ></b-form-input>
                                  </div>
                                </div>
                              </div>
                              <div class="col-3">
                                <b-button
                                  block
                                  variant="light"
                                  @click="actualizar_dato(item_producto.id,'precio_2',precio_2)"
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



                              <div class="col-8 mb-4">
                                <div class="row">
                                  <div class="col-2">
                                    <i class="fas fa-camera-retro fa-2x text-success"></i>
                                  </div>
                                  <div class="col-10">

                                      <!-- <b-form-file size="sm" ref="cony" id="cony" @change="captar_foto"   placeholder="Seleccione un logo"></b-form-file> -->
                                      <input name="imagen" class="imagen form-control" type="file" accept="image/*" @change="preview_image">
                                      <img class="thumbnail" id="output_image"/>


                                  </div>

                                </div>
                              </div>
                              <div class="col-3">
                                <b-button
                                v-if="!load_img"
                                  block
                                  variant="light"
                                  @click="actualizar_imagen(item_producto.id)"
                                >
                                  <i class="fas fa-edit text-success"></i>
                                </b-button>
                                <b-spinner v-if="load_img" variant="success" label="Spinning"></b-spinner>
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
                                @click="hideModalEditarProducto(item_producto.id)"
                              >Volver</b-button>
                            </div>
                          </div>
                        </b-modal>
      </div>
    </div>
  </div>
</template>

<script src="../administrarProducto/administrarProducto.js"></script>
<style scoped src="../administrarProducto/administrarProducto.css"></style>
<style src="vue-multiselect/dist/vue-multiselect.min.css"></style>
