<template>
  <div>
    <div class="row my-4 mx-1">
      <div class="col-12">
        <b-card class="text-center transparencia">
          <div class="row">
            <!-- buscar por codigo -->
            <div class="col-12 col-md-8">
              <b-card class="largoCard">
                <div class="row">
                  <div class="col-12">
                    <b-input-group>
                      <b-form-input
                        id="inputBuscar"
                        v-on:keyup="escribiendoProducto"
                        size="sm"
                         placeholder="Escanee el producto o ingrese el código de barras SKU"
                         v-model="buscadorProducto"
                         v-on:keyup.enter="traer_producto()"
                      ></b-form-input>
                      <b-input-group-append>
                        <b-button
                          id="btnBuscar"
                          :disabled="btn_buscar_producto"
                          block
                          variant="success"
                          @click="traer_producto()"
                          size="sm"
                        >Buscar</b-button>
                      </b-input-group-append>
                    </b-input-group>
                  </div>

                  <div class="col-12 my-4">
                    <div>
                      <b-table
                        table
                        hove12
                        hovexl-r
                        bordered
                        small
                        size="sm"
                        :fields="carroFieldsAdm"
                        :items="arregloCarro"
                        sticky-header="400px"
                        head-variant="dark"
                      >
                        <template v-slot:cell(sku)="data">{{ data.item.sku }}</template>
                        <template v-slot:cell(cantidad)="data">
                          <b-input
                            v-on:keyup.enter="ingresar_cantidad_carro(data.index,$event)"
                            block
                            type="number"
                            pill
                            size="sm"
                            :value="data.item.cantidad_ls "
                          ></b-input>
                        </template>
                        <template v-slot:cell(producto)="data">
                          <b>{{ data.item.nombre }}</b>
                          <br />
                          <em>{{ formatPrice(data.item.cantidad)}} unidades disponibles</em>
                        </template>
                        <template v-slot:cell(precioProd)="data">$ {{ formatPrice(data.item.precio_venta) }}</template>
                        <template
                          v-slot:cell(subtotal)="data"
                        >$ {{ formatPrice(data.item.precio_venta * data.item.cantidad_ls) }}</template>
                        <template v-slot:cell(opc)="data">
                          <div class="col-12 col-xl-12">
                            <b-button
                              size="sm"
                              pill
                              variant="danger"
                              @click="eliminarItem(data.index)"
                            >x</b-button>
                          </div>
                        </template>
                      </b-table>
                    </div>
                  </div>
                </div>

                <template v-slot:footer>
                  <!-- Total Temporal -->
                  <div class="row justify-content-end">
                    <div class="col-4">
                      <b-button
                        size="sm"
                        pill
                        variant="danger"
                        id="limpiarTodo"
                        @click="limpiarCarro()"
                      >Quitar todo</b-button>
                    </div>

                    <div class="col-4">
                      <label>
                        Total:
                        <b>$ {{formatPrice(total)}}</b>
                      </label>
                    </div>
                  </div>
                  <!-- Total Temporal -->
                </template>
              </b-card>
            </div>
            <!-- buscar por codigo -->

            <!-- tipo de venta -->
            <div class="col-12 col-md-4 text-right">
              <b-card>
                <div>
                  <b-form-group label="Forma de pago">
                    <b-form-checkbox-group
                      v-model="formaPago"
                      :options="formaPagoOpcion"
                      name="buttons-1"
                      button-variant="outline-success"
                      buttons
                      size="sm"
                    ></b-form-checkbox-group>
                  </b-form-group>
                </div>

                <div v-if="formaPago == '1' || formaPago == '1,2' || formaPago =='2,1'">
                  <b-form-group label="Monto en CLP">
                    <b-input-group>
                      <b-form-input size="sm" type="number" placeholder="Ingrese el monto que cancela el cliente" v-model="montoEfectivo">{{formatPrice()}}</b-form-input>
                      <b-input-group-append>
                        <b-button size="sm" text="Button" disabled>Efectivo</b-button>
                      </b-input-group-append>
                    </b-input-group>
                  </b-form-group>
                </div>

                <div v-if="formaPago == '2' || formaPago == '1,2' || formaPago =='2,1'">
                  <b-form-group label="Monto en CLP">
                    <b-input-group>
                      <b-form-input size="sm" type="number" placeholder="Ingrese el monto que cancela el cliente" v-model="montoDebito"></b-form-input>
                      <b-input-group-append>
                        <b-button size="sm" text="Button" disabled>T.Debito</b-button>
                      </b-input-group-append>
                    </b-input-group>
                  </b-form-group>
                </div>

                <div v-if="formaPago == '3'">
                  <b-form-group label="Monto en CLP">
                    <b-input-group>
                      <b-form-input size="sm" type="number" placeholder="Ingrese el monto que cancela el cliente" v-model="montoCredito"></b-form-input>
                      <b-input-group-append>
                        <b-button size="sm" text="Button" disabled>T.Credito</b-button>
                      </b-input-group-append>
                    </b-input-group>
                  </b-form-group>
                </div>

                <div>
                  <b-form-group label="Seleccione Entrega">
                    <b-form-radio-group
                      id="btn-radios-1"
                      v-model="entrega"
                      :options="entregaOpcion"
                      button-variant="outline-success"
                      buttons
                      name="radios-btn-default"
                      size="sm"
                    ></b-form-radio-group>
                  </b-form-group>
                </div>

                <template v-slot:footer>
                  <!-- detalle venta -->
                  <label>
                    <b>Detalle de Venta</b>
                  </label>
                  <div class="row">
                    <div class="col-8">
                      <label>Total a Pagar</label>
                    </div>
                    <div class="col-4">
                      <label>$ {{formatPrice(total)}}</label>
                    </div>

                    <div class="col-8">
                      <label>Cliente Paga</label>
                    </div>
                    <div class="col-4">
                      <div v-if="formaPago == ''">
                        <label>$ 0</label>
                      </div>
                      <div v-if="formaPago == '1'">
                        <label>$ {{formatPrice(montoEfectivo)}}</label>
                      </div>
                      <div v-if="formaPago == '2'">
                        <label>$ {{formatPrice(montoDebito)}}</label>
                      </div>
                      <div v-if="formaPago == '3'">
                        <label>$ {{formatPrice(montoCredito)}}</label>
                      </div>
                      <!-- <div v-if="formaPago == '1,2' || formaPago =='2,1'">
                        <label>({{montoEfectivo}} + {{montoDebito}})</label>
                      </div> -->
                    </div>

                    <div class="col-8">
                      <label>Vuelto</label>
                    </div>
                    <div class="col-4">
                      <div v-if="formaPago == ''">
                        <label>$ 0</label>
                      </div>
                      <div v-if="formaPago == '1'">
                        <label>$ {{formatPrice(montoEfectivo - total)}}</label>
                      </div>
                      <div v-if="formaPago == '2'">
                        <label>$ {{formatPrice(montoDebito - total)}}</label>
                      </div>
                      <div v-if="formaPago == '3'">
                        <label>$ {{formatPrice(montoCredito - total)}}</label>
                      </div>
                    </div>
                  </div>
                  <!-- detalle venta -->

                  <!-- Comprobante -->
                  <div class="row justify-content-end">
                    <div class="col-12">
                      <!-- BOTON VENTAS -->
                      <div>
                        <b-button
                          pill
                          block
                          size="sm"
                          id="show-btn"
                          class="my-2"
                          variant="success"
                          @click="registrar_venta()"
                        >Confirmar Compra</b-button>
                        <!-- @click="showModal();" -->
                      </div>
                      <!-- MODAL VENTAS  -->
                      <template>
                        <div>
                          <b-modal
                            class="modal-header-ventas"
                            id="modal-md"
                            size="md"
                            :ref="'ventasModal'"
                            hide-footer
                            centered
                          >
                            <section class="A7 width-82mm">
                              <!--Tabla Datos de Empresa-->
                              <table>
                                <!--Titulo-->
                                <thead>
                                  <!--Logotipo-->
                                  <tr>
                                    <th class="logo text-center" style="width:100%" colspan="4">
                                      <img
                                        src="data:image/jpeg;base64,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"
                                      />
                                    </th>
                                  </tr>
                                  <!--Logotipo-->

                                  <!--Datos Empresa-->
                                  <tr>
                                    <th class="border-black" style="width:100%" colspan="4">
                                      <h1
                                        class="top-0 bottom-0 text-left line-height-140 font-14"
                                      >Neo-Gestion</h1>
                                      <h3
                                        class="top-0 bottom-0 text-left line-height-130 font-11 light"
                                      >Neo-Gestion, maipo 842, los angeles Los Angeles Bio Bio, Chile</h3>
                                    </th>
                                  </tr>
                                  <!--Datos Empresa-->
                                </thead>
                                <!--Titulo-->
                                <!--Tbody-->
                                <tbody>
                                  <!--Fecha Emisión-->
                                  <tr>
                                    <td
                                      class="font-13 bold padding-small padding-top width-50"
                                      colspan="2"
                                    >Fecha : 12-feb-2020</td>
                                    <td
                                      class="font-13 bold padding-small padding-top"
                                      colspan="2"
                                    >Hora : 11:53:15</td>
                                  </tr>
                                  <!--Tipo Documento y folio-->
                                  <tr>
                                    <td
                                      class="font-13 bold padding-small"
                                      colspan="2"
                                    >Comprobante de Venta</td>
                                    <td class="font-13 bold padding-small" colspan="2">Nº 2</td>
                                  </tr>
                                  <!--Cajero-->
                                  <tr>
                                    <td class="padding-small" colspan="4">Cajero: david luna -</td>
                                  </tr>
                                  <!-- Cajero -->
                                  <!--Cajero-->
                                  <tr>
                                    <td class="font-13 bold padding-small" colspan="2">Transbank</td>
                                    <td class="font-13 bold padding-small" colspan="2">Nº 3.000</td>
                                  </tr>
                                  <!-- Cajero -->
                                </tbody>
                                <!--Tbody-->
                              </table>
                              <!--Tabla Datos de Empresa-->

                              <!--Cliente-->
                              <table class="top-10">
                                <tbody>
                                  <tr>
                                    <td class="border-bottom">
                                      <h3
                                        class="top-0 bottom-0 text-left line-height-130 font-12 bold"
                                      >Genérico</h3>
                                      <h3
                                        class="top-0 bottom-0 text-left line-height-130 font-12 light"
                                      >RUT: 1-9</h3>
                                      <h3
                                        class="top-0 bottom-0 text-left line-height-130 font-12 light"
                                      >Giro: Servicios</h3>
                                      <h3
                                        class="top-0 bottom-0 text-left line-height-130 font-12 light"
                                      >Comuna: Providencia</h3>
                                      <h3
                                        class="top-0 bottom-0 text-left line-height-130 font-12 light"
                                      >Cuidad: Santiago</h3>
                                      <h3
                                        class="top-0 bottom-0 text-left line-height-130 font-12 light"
                                      >Dirección: Padre Mariano 210</h3>
                                    </td>
                                  </tr>
                                </tbody>
                              </table>
                              <!--Cliente-->

                              <!--Tabla Detalles de Venta-->
                              <table class="top-10">
                                <tbody>
                                  <tr>
                                    <td class="font-14 light padding-top" colspan="3">Detalle</td>
                                  </tr>
                                  <tr>
                                    <td class="font-13 bold width-65 padding-small">Producto</td>
                                    <td class="font-13 bold width-5 padding-small">Cant.</td>
                                    <td
                                      class="font-13 bold text-right width-30 padding-small"
                                    >Precio</td>
                                  </tr>

                                  <!--Producto-->
                                  <tr>
                                    <td>MANZANA ($ 600 C/U)</td>
                                    <td>5</td>
                                    <td class="text-right">3.000</td>
                                  </tr>
                                  <!--Producto-->

                                  <!--Totales-->
                                  <tr>
                                    <td
                                      class="padding-small border-top bold font-16 text-right"
                                    >Total</td>
                                    <td class="padding-small border-top text-right font-16">$</td>
                                    <td
                                      class="padding-small border-top text-right font-16 bold"
                                    >3.000</td>
                                  </tr>

                                  <tr>
                                    <td class="padding-small text-right">T. Débito</td>
                                    <td class="padding-small text-right">$</td>
                                    <td class="padding-small text-right">3.000</td>
                                  </tr>
                                  <tr>
                                    <td class="padding-small text-right">Total Pagos</td>
                                    <td class="padding-small text-right">$</td>
                                    <td class="padding-small text-right">3.000</td>
                                  </tr>

                                  <tr>
                                    <td class="padding-small text-right">Vuelto</td>
                                    <td class="padding-small text-right">$</td>
                                    <td class="padding-small text-right">0</td>
                                  </tr>
                                  <!--Totales-->

                                  <!--Totales-->
                                </tbody>
                              </table>
                              <!--Tabla Detalles de Venta-->

                              <table class="top-20 bottom-20">
                                <tbody>
                                  <tr>
                                    <td class="padding-bottom-20">
                                      <h3
                                        class="top-0 bottom-0 text-center line-height-130 font-12 light"
                                      >Neo-POS</h3>
                                      <h3
                                        class="top-0 bottom-0 text-center line-height-130 font-12 light"
                                      >Comprobante de Venta</h3>
                                    </td>
                                  </tr>
                                </tbody>
                              </table>
                            </section>
                          </b-modal>
                        </div>
                      </template>
                    </div>
                  </div>
                  <!-- comprobante -->
                </template>
              </b-card>
            </div>
            <!--tipo de venta -->
          </div>

          <!-- SUCCESS VENTA -->
          <div class="col-12 mt-4">
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

          <!-- CAMPOS VACIOS O STOCK MAYOR -->
          <div class="col-12">
            <ul>
              <b-alert
                variant="warning"
                :show="dismissCountDown6"
                @dismissed="dismissCountDown6=0"
                @dismiss-count-down="countDownChanged6"
              >
                <p>{{errores6}} {{ dismissCountDown6 }} segundos...</p>
                <b-progress
                  variant="warning"
                  height="4px"
                  :max="dismissSecs6"
                  :value="dismissCountDown6"
                ></b-progress>
              </b-alert>
            </ul>
          </div>

          <!-- PRODUCTO REPETIDO -->
          <div class="col-12">
            <ul>
              <b-alert
                variant="warning"
                :show="dismissCountDown5"
                @dismissed="dismissCountDown5=0"
                @dismiss-count-down="countDownChanged5"
              >
                <p>El producto ya fue agregado al carro, modifique la cantidad en la tabla. {{ dismissCountDown5 }} segundos...</p>
                <b-progress
                  variant="warning"
                  height="4px"
                  :max="dismissSecs5"
                  :value="dismissCountDown5"
                ></b-progress>
              </b-alert>
            </ul>
          </div>

        </b-card>
      </div>
    </div>
  </div>
</template>


<script src="../generarVenta/generarVenta.js"></script>
<style scoped src="../generarVenta/generarVenta.css"></style>
