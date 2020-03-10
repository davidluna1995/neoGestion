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
                        <template
                          v-slot:cell(precioProd)="data"
                        >$ {{ formatPrice(data.item.precio_venta) }}</template>
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
                      <b-form-input
                        size="sm"
                        type="number"
                        placeholder="Ingrese el monto que cancela el cliente"
                        v-model="montoEfectivo"
                      >{{formatPrice()}}</b-form-input>
                      <b-input-group-append>
                        <b-button size="sm" text="Button" disabled>Efectivo</b-button>
                      </b-input-group-append>
                    </b-input-group>
                  </b-form-group>
                </div>

                <div v-if="formaPago == '2' || formaPago == '1,2' || formaPago =='2,1'">
                  <b-form-group label="Monto en CLP">
                    <b-input-group>
                      <b-form-input
                        size="sm"
                        type="number"
                        placeholder="Ingrese el monto que cancela el cliente"
                        v-model="montoDebito"
                      ></b-form-input>
                      <b-input-group-append>
                        <b-button size="sm" text="Button" disabled>T.Debito</b-button>
                      </b-input-group-append>
                    </b-input-group>
                  </b-form-group>
                </div>

                <div v-if="formaPago == '3'">
                  <b-form-group label="Monto en CLP">
                    <b-input-group>
                      <b-form-input
                        size="sm"
                        type="number"
                        placeholder="Ingrese el monto que cancela el cliente"
                        v-model="montoCredito"
                      ></b-form-input>
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
                      </div>-->
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
                            <section class="A7 width-82mm" id="printVenta">
                              <!--Tabla Datos de Empresa-->
                              <table>
                                <!--Titulo-->
                                <thead>
                                  <!--Logotipo-->
                                  <tr>
                                    <th class="logo text-center" style="width:100%" colspan="4">
                                     <img :src="listarConf.logo" v-show="logoNull" width="177px" height="86px">
                                    </th>
                                  </tr>
                                  <!--Logotipo-->

                                  <!--Datos Empresa-->
                                  <tr align="center">
                                    <th class="border-black" style="width:100%" colspan="4">
                                      <h1
                                        class="top-0 bottom-0 text-left line-height-140 font-14"
                                      >{{listarConf.empresa}}</h1>
                                      <h3
                                        class="top-0 bottom-0 text-left line-height-130 font-11 light"
                                      >{{listarConf.direccion}}</h3>
                                    </th>
                                  </tr>
                                  <!--Datos Empresa-->
                                </thead>
                                <!--Titulo-->
                                <!--Tbody-->
                                <tbody>
                                  <!--Fecha Emisión-->
                                  <tr v-for="t in ticketPrint" :key="t.id">
                                    <td
                                      class="font-13 bold padding-small padding-top width-50"
                                      colspan="4"
                                    >Fecha: {{t.fechaVenta}}</td>
                                  </tr>
                                    <tr v-for="t in ticketPrint" :key="t.id">
                                      <td
                                        class="font-13 bold padding-small"
                                        colspan="4"
                                      >Comprobante de Venta</td>
                                      <td class="font-13 bold padding-small" colspan="2">Nº {{t.idVenta}}</td>
                                    </tr>
                                  
                                  <!-- <tr>
                                    <td
                                      class="font-13 bold padding-small padding-top"
                                      colspan="4"
                                    >Hora: {{horaLocal}}</td>
                                  </tr>-->
                                  <!--Tipo Documento y folio-->
                                  <!-- <tr>
                                    <td
                                      class="font-13 bold padding-small"
                                      colspan="2"
                                    >Comprobante de Venta</td>
                                    <td class="font-13 bold padding-small" colspan="2">Nº 2</td>
                                  </tr>-->
                                  <!--Cajero-->
                                  <tr>
                                    <td class="font-13 bold padding-small" colspan="4">Cajero: {{usuario.name}}</td>
                                  </tr>
                                  <!-- Cajero -->
                                  <!--Cajero-->
                                  <!-- <tr>
                                    <td class="font-13 bold padding-small" colspan="2">Transbank</td>
                                    <td class="font-13 bold padding-small" colspan="2">Nº 3.000</td>
                                  </tr>-->
                                  <!-- Cajero -->
                                </tbody>
                                <!--Tbody-->
                              </table>
                              <!--Tabla Datos de Empresa-->

                              <!--Cliente-->
                              <!-- <table class="top-10">
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
                              </table>-->
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
                                  <tr v-for="t in ticketPrintDetalle" :key="t.id">
                                    <td>{{t.nombre}} ($ {{formatPrice(t.precio)}} C/U)</td>
                                    <td>{{t.cantidadDetalle}}</td>
                                    <td class="text-right">{{ formatPrice(t.precio * t.cantidadDetalle) }}</td>
                                  </tr>
                                  <!--Producto-->

                                  <!--Totales-->
                                  <tr v-for="t in ticketPrint" :key="t.id">
                                    <td
                                      class="padding-small border-top bold font-16 text-right"
                                    >Total</td>
                                    <td class="padding-small border-top text-right font-16">$</td>
                                    <td
                                      class="padding-small border-top text-right font-16 bold"
                                    >{{formatPrice(t.totalVenta)}}</td>
                                  </tr>

                                  <!-- <tr>
                                    <td class="padding-small text-right">T. Débito</td>
                                    <td class="padding-small text-right">$</td>
                                    <td class="padding-small text-right">3.000</td>
                                  </tr>
                                  <tr>
                                    <td class="padding-small text-right">Total Pagos</td>
                                    <td class="padding-small text-right">$</td>
                                    <td class="padding-small text-right">3.000</td>
                                  </tr>-->

                                  <tr>
                                    <td class="padding-small text-right">Vuelto</td>
                                    <td class="padding-small text-right">$</td>
                                    <td class="padding-small text-right">
                                      <div v-if="formaPago == '1'">
                                        <label>$ {{formatPrice(montoEfectivo - total)}}</label>
                                      </div>
                                      <div v-if="formaPago == '2'">
                                        <label>$ {{formatPrice(montoDebito - total)}}</label>
                                      </div>
                                      <div v-if="formaPago == '3'">
                                        <label>$ {{formatPrice(montoCredito - total)}}</label>
                                      </div>
                                    </td>
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
                            <div class="row justify-content-center bordeFooter">
                              <div class="col-4">
                                <b-button
                                  class="my-2"
                                  block
                                  pill
                                  variant="info"
                                  v-print="printVenta"
                                  @click="hideModal()"
                                >imprimir</b-button>
                              </div>
                            </div>
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
