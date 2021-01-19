<template>
  <div class="mt-4" v-if="usuario.rol==admin">
    <!-- <div class="col-12 col-lg-12">
        <b-card class="text-center transparencia mb-4">
          <b-alert show variant="warning">
            <b>NOTA:</b>
            <em>
              Al cambiar algun dato de estos formularios por seguridad la
              sesión se cerrará automaticamente para que efectue un nuevo
              inicio de sesión con los nuevos datos.
            </em>
    </b-alert>-->

    <!-- ALERT SUCCESS-->
    <!-- <div class="col-12 text-center mx-auto my-4">
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
    </div>-->
    <!-- ALERT SUCCESS -->
    <!-- </b-card>
    </div>-->

    <div class="col-12 col-lg-12">
      <b-card class="text-center tituloTabla transparencia mb-4">
        <b-card-header class="fondoCategoria mb-4">REPORTE DE VENTAS</b-card-header>

        <div class="row justify-content-center">


            <div class="col-12 col-md-4 col-lg-3" >
                        <label>Desde:</label>
                        <b-form-input size="sm" type="date" v-model="desde" v-on:keyup.enter="traer_ventas()"></b-form-input>
                        <input v-model="hora_d" class="form-control form-control-sm my-2" type="time" value="00:00">
            </div>

            <div class="col-12 col-md-4 col-lg-3">
                        <label>Hasta:</label>
                        <b-form-input size="sm" type="date" v-model="hasta" v-on:keyup.enter="traer_ventas()"></b-form-input>
                        <input v-model="hora_h" class="form-control form-control-sm my-2" value="23:59" type="time" >
            </div>

            <div class="col-12 col-md-4 col-lg-3">
                        <label>Opciones:</label>
                        <b-button :disabled="btn_filtrar" size="sm" block variant="success" @click="traer_ventas()">Filtrar</b-button>
                        <b-button size="sm" block variant="success" @click="limpiar()">Reiniciar</b-button>
            </div>



                    <!-- <div class="col-12 col-md-2 col-lg-2 mt-4">
                        <b-button block variant="success" v-print="printVenta">Imprimir Ventas</b-button>
                    </div> -->


        </div>
          <br>
         <div v-if="filtro">
          <div class="row justify-content-center">
            <div class="col-md-11">



                <div class="table-responsive">
                    <table id="cabeza" class="table table-bordered">
                    <tr>
                        <td colspan="12" style="background:#343a40; color:white">{{resumen_titulo}}</td>
                    </tr>
                    <tr>
                    <th style="background:#343a40; color:white">venta total</th>
                    <td><span class="green">$</span> {{ formatPrice(suma_ventas) }}</td>
                    <th style="background:#343a40; color:white">efectivo real</th>
                    <td>$ {{formatPrice(efectivo_real)}}</td>
                    <th style="background:#343a40; color:white">Debito</th>
                    <td>$ {{formatPrice(debito)}}</td>
                    <th style="background:#343a40; color:white">Credito </th>
                    <td>$ {{ formatPrice(credito) }}</td>
                    <th style="background:#343a40; color:white">Vuelto </th>
                    <td>$ {{ formatPrice(vuelto) }}</td>
                    </tr>
                    </table>
                </div>
            </div>

            <div class="col-md-1">
                 <button class="btn btn-outline-success  btn-sm my-4" @click="exportar_tabla(listarReporteVentas)">
                     <i class="fas fa-file-csv fa-3x"></i>
                     </button>
            </div>
          </div>

          <div class="row">
            <div class="col-12">
              <div>
                <b-table
                  id="tabla_excel"
                  show-empty
                  emptyText="Seleccione un rango de fechas."
                  small
                  striped
                  hover
                  bordered
                  stacked="lg"
                  head-variant="dark"
                  :fields="reporteVentasFieldsAdm"
                  :items="listarReporteVentas"
                >
                  <template v-slot:cell(index)="data">{{ data.item.idVenta }}</template>

                  <template v-slot:cell(venta)="data">
                    <span class="green">$</span>
                    {{ formatPrice(data.item.venta_total) }}
                  </template>

                  <template v-slot:cell(fecha)="data">{{data.item.creado}}</template>
                  <template v-slot:cell(dte)="data">Dte {{data.item.dte}}</template>
                  <template v-slot:cell(creado)="data">{{data.item.nombreUsuarioVenta}}</template>
                  <template v-slot:cell(cliente)="data">{{data.item.cliente}}</template>
                  <template v-slot:cell(tipo_pago)="data"><small>{{ 'Efectivo: '+formatPrice((data.item.pago_efectivo)?data.item.pago_efectivo : 0) }} <br>
                                                       {{ 'Debito: '+formatPrice((data.item.pago_debito)?data.item.pago_debito : 0)}}   </small>
                  </template>
                  <template v-slot:cell(credito)="data">
                       {{ formatPrice((data.item.pago_credito)?data.item.pago_credito : 0) }} <br>
                  </template>
                   <!-- <template v-slot:cell(deuda_credito)="data">
                       <label style="color:red" v-if="((data.item.monto_credito)?data.item.monto_credito : 0)>0">
                           $ {{ formatPrice((data.item.monto_credito)?data.item.monto_credito : 0) }}
                        </label>
                       <label style="color:black" v-if="((data.item.monto_credito)?data.item.monto_credito : 0)==0">
                          $ {{ formatPrice((data.item.monto_credito)?data.item.monto_credito : 0) }}
                        </label>
                   </template> -->
                   <template v-slot:cell(vuelto)="data">
                       <label style="color:red" v-if="((data.item.vuelto)?data.item.vuelto : 0) > 0"> $ {{ formatPrice((data.item.vuelto)?data.item.vuelto : 0) }}</label>
                       <label style="color:black" v-if="((data.item.vuelto)?data.item.vuelto : 0) == 0"> $ {{ formatPrice((data.item.vuelto)?data.item.vuelto : 0) }}</label>
                   </template>
                   <template v-slot:cell(detalle)="data">
                    <!-- EDITAR PRODUCTOS -->
                    <!-- BOTON EDITAR -->
                    <div class="row">
                      <div class="col-12">
                        <b-button
                          block
                          id="show-btn"
                          class="my-2"
                          variant="success"
                          @click="showModalDetalleVenta(data.item.idVenta,data.item.idVenta)"
                        >Ver Detalle</b-button>
                      </div>
                    </div>
                    <div class="col-12 col-xl-12">
                      <!-- MODAL EDITAR PRODUCTOS -->
                      <template>
                        <div>
                          <b-modal
                            hide-footer
                            centered
                            class="modal-header-editar"
                            id="modal-xl"
                            size="xl"
                            :ref="'reporte'+data.item.idVenta"
                          >
                            <template v-slot:modal-title>
                              <h5 class="text-center">Resumen de la venta</h5>
                            </template>
                            <div id="printDetalle">
                              <h5 class="text-center">Detalle de la venta: {{data.item.idVenta}}</h5>
                              <b-table
                                show-empty
                                emptyText="No existen productos aun."
                                small
                                striped
                                hover
                                bordered
                                stacked="lg"
                                head-variant="dark"
                                :fields="reporteDetalleVentaFieldsAdm"
                                :items="listarReporteDetalleVentas"
                              >
                              <template v-slot:cell(imagen)="data"><b-img class="tamanio" thumbnail v-if="data.item.imagen"  :src="data.item.imagen" alt="Image 1"></b-img></template>
                                <template v-slot:cell(nombre)="data">{{ data.item.nombre }}</template>
                                <template v-slot:cell(descripcion)="data">{{ data.item.proDesc }}</template>
                                <template v-slot:cell(categoria)="data">{{ data.item.catDesc }}</template>
                                <template v-slot:cell(unidad)="data">{{ data.item.unidad }}</template>
                                <template
                                  v-slot:cell(precio)="data"
                                >{{ formatPrice(data.item.precio) }}</template>
                                <template v-slot:cell(cantidad)="data">{{ data.item.cantidadDetalle }}</template>
                                <template v-slot:cell(descuento)="data">{{ data.item.descuento }}</template>
                                <template v-slot:cell(_descuento)="data">{{ data.item.porcentaje_descuento }}</template>
                                <template v-slot:cell(impuesto_adicional)="data">{{ data.item.impuesto_adicional }}</template>
                                <template v-slot:cell(tipo_impuesto_adicional)="data">{{ data.item.tipo_impuesto_adicional }}</template>
                                <template v-slot:cell(afecto)="data">{{ data.item.afecto_iva }}</template>
                              </b-table>
                            </div>

                            <div class="row justify-content-center bordeFooter">
                              <!-- <div class="col-4">
                                <b-button
                                  class="my-2"
                                  block
                                  pill
                                  variant="success"
                                  v-print="printDetalle"
                                >Imprimir Detalle</b-button>
                              </div> -->
                              <div class="col-4">
                                <b-button
                                  class="my-2"
                                  block
                                  pill
                                  variant="info"
                                  @click="hideModalDetalleVenta(data.item.idVenta,data.item.idVenta)"
                                >Volver</b-button>
                              </div>
                            </div>
                          </b-modal>
                        </div>
                      </template>
                    </div>
                  </template>
                   <template v-slot:cell(comprobante)="data">
                      <div>
                          <button :disabled="load_comprobante" class="btn btn-sm btn-success my-2" @click="abrir_venta('comprobante', data.item.idVenta)">
                              <i  class="fas fa-file-invoice "></i> {{data.item.dte}}
                               <!-- <span v-if="load_comprobante" class="sr-only">Loading...</span> -->
                          </button>

                      <!-- BOTON VENTAS -->




                  <!-- comprobante -->
                      </div>
                  </template>
                </b-table>


                <!-- MODAL VENTAS  -->

                        <div>
                          <b-modal
                            no-close-on-esc
                            no-close-on-backdrop
                            class="modal-header-ventas"
                            id="modal-md"
                            size="md"
                            :ref="'comprobante'"
                            hide-footer
                            centered
                          >

                          <!-- <pre>{{factura}}</pre> -->
                            <section v-if="totales.dte == '1'">
                             <div
                                class="ticket"
                                id="printVenta"
                                style="
                                    font-size: 12px;
                                    font-family: 'Times New Roman';
                                "
                                >
                                <center>
                                    <img
                                    :src="listarConf.logo"

                                    width="177px"
                                    height="86px"
                                    />
                                </center>
                                <center>
                                    <p>
                                    <!-- TICKET DE VENTA -->
                                    BOLETA ELECTRONICA NUMERO {{ticketPrint.id}}
                                    <br />
                                    {{ listarConf.empresa }}
                                    <br>
                                    {{ listarConf.rut }}
                                    <br>
                                    {{listarConf.giro}}

                                    <br />
                                    {{ listarConf.direccion }}
                                    </p>
                                </center>
                                <table align="center">
                                    <thead>
                                    <!--Fecha Emisión-->
                                    <tr>
                                        <th colspan="4">
                                        Fecha: {{ ticketPrint.fecha }}
                                        </th>
                                    </tr>
                                    <tr>
                                        <th colspan="4">
                                        Comprobante de Venta Nº {{ ticketPrint.id }}
                                        </th>
                                    </tr>

                                    <tr
                                        style="
                                        border-top: 1px solid black;
                                        border-collapse: collapse;
                                        "
                                    >
                                        <th
                                        style="
                                            border-top: 1px solid black;
                                            border-collapse: collapse;
                                        "
                                        >
                                        PRODUCTO
                                        </th>
                                        <th
                                        style="
                                            border-top: 1px solid black;
                                            border-collapse: collapse;
                                        "
                                        >
                                        CANT
                                        </th>
                                        <th
                                        style="
                                            border-top: 1px solid black;
                                            border-collapse: collapse;
                                        "
                                        >
                                        PRECIO
                                        </th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <!--Producto-->
                                    <tr
                                        v-for="t in ticketPrintDetalle"
                                        :key="t.id"
                                    >
                                        <td>
                                        {{ t.nombre }} ($
                                        {{ formatPrice(t.precio) }} C/U)
                                        </td>
                                        <td>{{ t.cantidad }}</td>
                                        <td class="text-right">
                                        {{
                                            formatPrice(
                                            t.precio * t.cantidad
                                            )
                                        }}
                                        </td>
                                    </tr>
                                    <!--Producto-->
                                    <br />
                                    <!--Totales-->
                                    <tr>
                                        <td>
                                        <b>Total:</b>
                                        $ {{ formatPrice(ticketPrint.venta_total) }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                        <b>IVA:</b>
                                        $ {{ formatPrice(ticketPrint.totales_iva) }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                        <div>
                                            <label>
                                            <b>Vuelto:</b>
                                            $ {{ formatPrice(ticketPrint.vuelto) }} <label v-if="ticketPrint.vuelto < 0" for=""> Deuda de cliente</label>
                                            </label>
                                        </div>
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                                <br />
                                <center>
                                    <p class="centrado">
                                    <b>Cliente: </b>{{ ticketPrint.cliente }}
                                    </p>
                                    <p class="centrado">
                                    ¡GRACIAS POR SU COMPRA!
                                    </p>
                                    <p>NEO-GESTION</p>
                                    <p>.................</p>
                                    <!-- AQUI SE GENERA EL TIMBRE ELECTRONICO -------------------------------------------------------->
                                                            <div style="text-align: center"><br>
                                                                <!-- insert your custom barcode setting your data in the GET parameter "data" -->
                                                                <img
                                                                width="90%"
                                                                height="120%"
                                                                alt="Barcode Generator TEC-IT"
                                                                :src="'https://barcode.tec-it.com/barcode.ashx?data=<TED VERSION=1.0><RUT>18805652-0</RUT><RAZONSOCIAL>NEOFOX INFORMATICA LIMITADA</RAZONSOCIAL><esto es una prueba solo para alargar el xml que en realidad no es nada asi que no mames wey sorry por esto no señor jesuuuuuuu></TED><TED VERSION=1.0><RUT>18805652-0</RUT><RAZONSOCIAL>NEOFOX INFORMATICA LIMITADA</RAZONSOCIAL><esto es una prueba solo para alargar el xml que en realidad no es nada asi que no mames wey sorry por esto no señor jesuuuuuuu></TED><TED VERSION=1.0><RUT>18805652-0</RUT><RAZONSOCIAL>NEOFOX INFORMATICA LIMITADA</RAZONSOCIAL><esto es una prueba solo para alargar el xml que en realidad no es nada asi que no mames wey sorry por esto no señor jesuuuuuuu></TED><TED VERSION=1.0><RUT>18805652-0</RUT><RAZONSOCIAL>NEOFOX INFORMATICA LIMITADA</RAZONSOCIAL><esto es una prueba solo para alargar el xml que en realidad no es nada asi que no mames wey sorry por esto no señor jesuuuuuuu></TED>&code=PDF417&multiplebarcodes=false&translate-esc=false&unit=Fit&dpi=96&imagetype=Gif&rotation=0&color=%23000000&bgcolor=%23ffffff&codepage=Default&qunit=Mm&quiet=0'"
                                                                />
                                                            </div>
                                                            <div style="font-size: 1.2rem; font-family: sans-serif">
                                                                <!-- back-linking to www.tec-it.com is required -->

                                                                <!-- logos are optional -->
                                                                <center>
                                                                    Timbre electronico SII <br>
                                                                    Verifique en www.sii.cl
                                                                </center>

                                                            </div>
                                </center>


                                </div>
                            </section>


                            <section v-if="totales.dte == '33'">
                                <div  id="printVenta">

                                <div >
                                    <center style="font-size: 2rem; border:3px solid #A93226;color:#A93226;font-family:sans-serif;width:90%">
                                    <b class="upper"><pre style="color:#A93226">R.U.T {{ factura.emisor.rut }}</pre></b>
                                    <b><pre style="color:#A93226">FACTURA ELECTRONICA</pre></b>
                                    <b><pre style="color:#A93226">Nº {{ totales.folio }}</pre></b>
                                    </center>
                                    <br />
                                    <center style="font-size: 2rem;font-family:sans-serif; width:90%">
                                    <pre> S.I.I LOS ANGELES</pre>
                                    </center>
                                    <!-- <br /> -->
                                    <center style="font-size: 2rem;font-family:sans-serif; width:90%">
                                    <pre> {{ factura.emisor.empresa }}</pre>
                                    </center>

                                    <!-- DATOS DEL EMISOR  -->
                                    <section
                                    class="datos_emisor"
                                    style="
                                        font-family: sans-serif;
                                        font-size: 1.2rem;
                                        width: 100%;
                                        white-space: pre-wrap;
                                        white-space: -moz-pre-wrap;
                                        white-space: -pre-wrap;
                                        white-space: -o-pre-wrap;
                                        word-wrap: break-word;
                                    "
                                    >
                                    <div style="width:90%">
                                        <label class="upper"><b>DIRECCION: </b> {{factura.emisor.direccion}}</label><br>
                                    <label class="upper"><b>GIRO: </b>{{ factura.emisor.giro }}</label> <br>


                                    <b>EMISION&nbsp;: </b> {{ factura.Fecha }} <br />
                                    <b class="upper">MEDIO DE PAGO&nbsp;: </b> {{ factura.FormaPago_str }} <br /><br>
                                    <b class="upper">SEÑOR(A)&nbsp;: </b> {{ factura.Cliente.RazonSocial }} <br />
                                    <b class="upper">RUT&nbsp;: </b> {{ factura.Cliente.Rut }} <br />
                                    <b class="upper">DIRECCION(A)&nbsp;: </b> {{ factura.Cliente.Direccion }} <br />
                                    <b class="upper">COMUNA(A)&nbsp;: </b> {{ factura.Cliente.Comuna }} <br />
                                    <b class="upper">CIUDAD(A)&nbsp;: </b> {{ factura.Cliente.Ciudad }} <br />

                                    <b class="upper">GIRO&nbsp;: </b> {{ factura.Cliente.Giro }} <br />
                                    </div>

                                    <table style="width:90%;">
                                        <tr>
                                        <td
                                            style="
                                            border-bottom: 1px solid black;
                                            border-top: 1px solid black;
                                            "
                                        >
                                            Item
                                        </td>
                                        <td
                                            style="
                                            border-bottom: 1px solid black;
                                            border-top: 1px solid black;
                                            "
                                        >
                                            P.unitario
                                        </td>
                                        <td
                                            style="
                                            border-bottom: 1px solid black;
                                            border-top: 1px solid black;
                                            "
                                        >
                                            Cantidad
                                        </td>
                                        <td
                                            style="
                                            border-bottom: 1px solid black;
                                            border-top: 1px solid black;
                                            "
                                        >
                                            Unidad
                                        </td>
                                        <td
                                            style="
                                            border-bottom: 1px solid black;
                                            border-top: 1px solid black;
                                            "
                                        >
                                            Total item
                                        </td>
                                        </tr>

                                        <tr v-for="f in factura.Productos " :key="f.id">
                                            <td>{{ f.NombreProducto  }}</td>
                                            <td>{{ formatPrice(f.PrecioNeto) }}</td>
                                            <td>{{ f.Cantidad }}</td>
                                            <td>{{ f.UnidadMedida }}</td>
                                            <!-- <td>{{ formatPrice(c.PrecioNeto * c.Cantidad) }}</td> -->
                                            <td>{{ formatPrice(Number((f.PrecioNeto * f.Cantidad) - f.DescuentoNeto) ) }}</td>
                                        </tr>

                                        <tr>
                                        <td
                                            class="fintabla"
                                            style="
                                            border-bottom: 1px solid black;
                                            border-top: 1px solid black;
                                            "
                                            colspan="3"
                                        >
                                            <div style="text-align: right">TOTAL NETO&nbsp;:</div>
                                        </td>
                                        <td
                                            colspan="2"
                                            class="fintabla"
                                            style="
                                            border-bottom: 1px solid black;
                                            border-top: 1px solid black;
                                            "
                                        >
                                        $ {{ formatPrice(totales.totales_neto) }}
                                        <!-- $ {{formatPrice(pre_factura.venta_total)}} -->
                                        </td>

                                        </tr>
                                        <tr>
                                            <td
                                            class="fintabla"
                                            style="
                                            border-bottom: 1px solid black;
                                            border-top: 1px solid black;
                                            "
                                            colspan="3"
                                        >
                                            <div style="text-align: right">IMP. ESPECIFICO&nbsp;:</div>
                                        </td>
                                        <td
                                            colspan="2"
                                            class="fintabla"
                                            style="
                                            border-bottom: 1px solid black;
                                            border-top: 1px solid black;
                                            "
                                        >
                                        $ {{ formatPrice(totales.totales_impuesto_especifico) }}
                                        <!-- $ {{formatPrice(pre_factura.venta_total)}} -->
                                        </td>
                                        </tr>

                                        <tr>
                                        <td
                                            class="fintabla"
                                            style="
                                            border-bottom: 1px solid black;
                                            border-top: 1px solid black;
                                            "
                                            colspan="3"
                                        >
                                            <div style="text-align: right">I.V.A 19%&nbsp;:</div>
                                        </td>
                                        <td
                                            class="fintabla"
                                            style="
                                            border-bottom: 1px solid black;
                                            border-top: 1px solid black;
                                            "
                                        >
                                            <!-- $ {{ formatPrice(((pre_factura.venta_total * 119) / 100) - pre_factura.venta_total  ) }} -->
                                            $ {{ formatPrice(totales.totales_iva)}}
                                        </td>
                                        </tr>

                                        <tr>
                                        <td
                                            class="fintabla"
                                            style="
                                            border-bottom: 1px solid black;
                                            border-top: 1px solid black;
                                            "
                                            colspan="3"
                                        >
                                            <div style="text-align: right">MONTO BRUTO&nbsp;:</div>
                                        </td>
                                        <td
                                        colspan="2"
                                            class="fintabla"
                                            style="
                                            border-bottom: 1px solid black;
                                            border-top: 1px solid black;
                                            "
                                        >
                                        <!-- $ {{formatPrice((pre_factura.venta_total * 119) / 100)}} -->
                                        $ {{ formatPrice(totales.venta_total)}}
                                        </td>
                                        </tr>
                                    </table>

                                    <!-- <div style="text-align:right">
                            Total: 10.000
                        </div> -->
                                    </section>
                                </div>

                                <!-- AQUI SE GENERA EL TIMBRE ELECTRONICO -------------------------------------------------------->
                                <div style="text-align: center"><br>

                                    <!-- insert your custom barcode setting your data in the GET parameter "data" -->
                                    <img
                                    width="100%"
                                    height="100%"
                                    alt="Barcode Generator TEC-IT"
                                    :src="'https://barcode.tec-it.com/barcode.ashx?data='+totales.ted+'&code=PDF417&multiplebarcodes=false&translate-esc=false&unit=Fit&dpi=96&imagetype=Gif&rotation=0&color=%23000000&bgcolor=%23ffffff&codepage=Default&qunit=Mm&quiet=0'"
                                    />
                                </div>
                                <div style="font-size: 1.2rem; font-family: sans-serif">
                                    <!-- back-linking to www.tec-it.com is required -->

                                    <!-- logos are optional -->
                                    <center>
                                        Timbre electronico SII <br>
                                        Verifique en www.sii.cl
                                    </center>

                                </div>
                                <!-- AQUI SE GENERA EL TIMBRE ELECTRONICO -------------------------------------------------------------->


                                </div>
                            </section>
                            <div class="row justify-content-center bordeFooter">
                              <div class="col-4">
                               <button
                                    type="button"
                                    class="btn btn-primary"
                                    onclick="printJS({
                                printable: 'printVenta',
                                type:'html',
                                style:'*{color:black}'
                                })"
                                    >
                                    Imprimir DTE
                                    </button>
                                  <!-- @click="hideModal()" -->
                              </div>
                            </div>
                          </b-modal>
                        </div>
              </div>
            </div>
          </div>
        </div>
      </b-card>
    </div>
  </div>
</template>

<script src="../reportesVentas/reportesVentas.js"></script>
<style scoped src="../reportesVentas/reportesVentas.css"></style>
