<template>
  <div>
    <button v-if="false" @click="generar_un_xml">Genera un xml factura</button>

    <button
    v-if="false"
      type="button"
      class="btn btn-primary"
      data-toggle="modal"
      data-target="#modalFactura"
    >
      Ver xml como una factura real
    </button>
    <!-- <button @click="consultar_folios"> consultar folios</button> -->

    <div v-if="false" class="card">
      aqui se veria la img
      {{ted}}
      <img width="700" height="400" :src="ted" alt="">
    </div>


      <!-- MODAL mensaje de caja o periodo  -->
    <template>
        <div>
          <b-modal
            no-close-on-esc
            no-close-on-backdrop
            class="modal-header-ventas"
            id="modal-periodo-caja"
            size="md"
            :ref="'modal-periodo-caja'"
            hide-footer
            centered
          >
            <template v-slot:modal-title>
                      <h5 class="text-center">MENSAJE IMPORTANTE!!</h5>
            </template>
            <section>
                <div v-if="estado_periodo == 'ACTIVO'" class="alert alert-primary" role="alert">
                    <small>Si en la parte superior aparece un boton verde "Periodo activo", no es necesario abrir nuevamente una caja, puede cerrar este modal.</small>
                </div>
              <div v-if="estado_periodo=='INACTIVO'">
                  <b>Iniciar nuevo periodo</b>
                  <br><br>
                  <div class="row">
                      <div class="col-md-6">
                            <label for="">Fecha inicio:</label>
                            <input v-model="fecha_inicio" class="form-control" type="date">
                      </div>
                      <div class="col-md-6">
                            <label for="">Hora inicio:</label>
                            <input v-model="hora_inicio" class="form-control" type="time">
                      </div>
                  </div>

              </div>

              <div v-if="estado_caja=='INACTIVO'">
                   <b>Abrir caja</b>
                   <br><br>
                   <label for=""><i class="fas fa-cash-register"></i> {{ nombre_caja.nombre }}</label>
                    <br>
                    <div v-if="estado_periodo=='ACTIVO' && estado_caja == 'INACTIVO'">
                        <div class="row">
                            <div class="col-md-6">
                                    <label for="">Fecha inicio:</label>
                                    <input v-model="fecha_inicio" class="form-control" type="date">
                            </div>
                            <div class="col-md-6">
                                    <label for="">Hora inicio:</label>
                                    <input v-model="hora_inicio" class="form-control" type="time">
                            </div>
                        </div>
                    </div>
                   <br>
                  <label for="">Monto de apertura:</label>
                  <input v-model="apertura_monto" class="form-control" type="numeric" placeholder="Monto de apertura..">
                <br>
                <div v-if="(estado_periodo=='INACTIVO' && estado_caja=='INACTIVO')">
                    <button @click="abrir_periodo_caja(fecha_inicio, hora_inicio, apertura_monto, nombre_caja )" class="btn btn-info btn-block"> Abrir periodo y caja</button>
                </div>


                <div v-if="estado_periodo=='ACTIVO' && estado_caja == 'INACTIVO'">
                    <button :disabled="btn_abrir_caja" @click="abrir_solo_caja(fecha_inicio, hora_inicio, apertura_monto, nombre_caja )" class=" btn-info btn btn-block"> Abrir caja</button>
                </div>

              </div>
            </section>
          </b-modal>
        </div>
     </template>
     <!-- MODAL mensaje de caja o periodo  -->
     <b-card class="text-center transparencia">
        <b-card class="largoCard">
            <!-- {{ modal estado_caja }} -->
            <button @click="cargar_datos_caja(nombre_caja.caja_id);abrir_modal('modal-cierre-caja-periodo');" v-if="estado_caja == 'ACTIVO'" class="btn btn-success btn-sm"><i class="fas fa-cash-register"></i> {{ nombre_caja.nombre+' '+estado_caja }}</button>

            <b-modal
                    no-close-on-esc
                    no-close-on-backdrop
                    class="modal-header-ventas"
                    id="modal-cierre-caja-periodo"
                    size="md"
                    :ref="'modal-cierre-caja-periodo'"
                    hide-footer
                    centered
            >
                    <template v-slot:modal-title>
                      <h5 class="text-center">{{nombre_caja.nombre}}</h5>
                    </template>

                    <section>
                        <b>Cierre de caja:</b>
                        <!-- <pre>{{ data_caja_periodo }}</pre> -->
                        <br>
                        <label for="">Estado:</label>{{ (data_caja_periodo.activo=='S')? 'ACTIVA' : 'INACTIVA' }} <br>

                        <label for="">Fecha y hora de apertura:</label>
                        {{ data_caja_periodo.fecha_inicio }} <br>
                        <label for="">Monto apertura:</label>
                        $ {{ formatPrice(data_caja_periodo.monto_inicio) }} <br>
                        <label for="">Monto de cierre:</label>
                        <label ><b>$ {{ formatPrice(capta_monto.suma_venta_total) }}</b></label> <br>
                        <label for="">Monto total debito:</label>
                        <label>$ {{ formatPrice(capta_monto.suma_pago_debito_total) }}</label> <br>
                        <label for="">Monto total efectivo - vuelto:</label>
                        <label for="">$ {{ formatPrice(capta_monto.suma_efectivo) }}</label> <br>



                        <label style="color:red" for="">La fecha y hora de cierre se capturan al cerrar la caja y/o periodo</label><br>
                        <!-- <button @click="captura_monto_cierre(data_caja_periodo)" class="btn btn-link btn-sm">Capturar monto de cierre</button> <br> -->
                        <button :disabled="btn_cerrar_caja" @click="cerrar_solo_caja(capta_monto.suma_venta_total, nombre_caja, data_caja_periodo)" class="btn btn-success btn-block">Cerrar caja</button>

                    </section>
            </b-modal>

    <!-- {{ estado_periodo }} -->
            <button @click="cargar_datos_periodo(nombre_caja.caja_id);abrir_modal('modal-cierre-periodo');" v-if="estado_periodo == 'ACTIVO'" class="btn btn-success btn-sm"><i class="fas fa-cash-register"></i> {{ 'Periodo activo' }}</button>

            <b-modal
                    no-close-on-esc
                    no-close-on-backdrop
                    class="modal-header-ventas"
                    id="modal-cierre-periodo"
                    size="xl"
                    :ref="'modal-cierre-periodo'"
                    hide-footer
                    centered
            >
                    <template v-slot:modal-title>
                      <h5 class="text-center">{{'Periodo activo'}}</h5>
                    </template>

                    <section>
                        <div class="alert alert-dark" role="alert">
                            El periodo debe cerrarse cuando ya no existan cajas activas en turno o jornada laboral
                        </div>
                        <center><b>Almacenado resumen de periodo</b></center>
                        <!-- <pre>{{ get_datos_periodo }}</pre> -->
                        <label style="color:#2C3E50" for="">Fecha y hora de apertura:</label>
                        {{ get_datos_periodo.fecha_inicio }} <br>
                        <label for="" style="color:#5D6D7E">La fecha y hora de cierre se capturan al cerrar periodo</label> <br>

                        <label style="color:#2C3E50" for="">Monto total de apertura (suma de las cajas activas):</label>
                            $ {{formatPrice(get_datos_periodo.monto_inicio)}} <br>

                        <label style="color:#2C3E50" for="">Monto total de cierre (suma de las cajas activas):</label>
                            $ {{formatPrice(get_datos_periodo.monto_cierre)}} <br>
                            <label style="color:#2C3E50" for="">Usuario de apertura de periodo (Primera caja activa):</label>
                            {{ get_datos_periodo.name }} <br>


                        <hr>

                        <div v-if="estado_caja == 'ACTIVO'">
                             <button :disabled="true" @click="cerrar_periodo(get_datos_periodo.id, estado_caja)" class="btn btn-success btn-block">Cerrar periodo</button>
                        </div>


                        <div v-if="estado_caja == 'INACTIVO'">
                             <button :disabled="false" @click="cerrar_periodo(get_datos_periodo.id, estado_caja)" class="btn btn-success btn-block">Cerrar periodo</button>
                        </div>

                    </section>
            </b-modal>

            <br>
            <!-- seleect de tipo de monto -->
            <label for="">Formato de precio:</label>
            <select v-model="dte_precio" class="form-control" name="" id="">
                <option  value="iva_incluido">Precio + iva incluido</option>
                <!-- <option  value="neto">Precio neto</option> -->
            </select>
        </b-card>
     </b-card>




    <!-- <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modalFactura">
        modal factura
    </button> -->
    <!-- Modal factura -->

    <b-modal ref="modal_factura" no-close-on-esc
                            no-close-on-backdrop hide-footer title="Facturación electronica (DTE 33)">

        <div class="modal-body">

            <div id="pdfFactura">
              <!-- AQUI EL DISEÑO DE LA FACTURA ELECTRONICA -->
              <div class="factura">
                <center style="font-size: 2rem; border:3px solid red;color:red;font-family:sans-serif;">
                  <b class="upper"><pre style="color:red">R.U.T {{ pre_factura.emisor.rut }}</pre></b>
                  <b><pre style="color:red">FACTURA ELECTRONICA</pre></b>
                  <b><pre style="color:red">Nº XXXX</pre></b>
                </center>
                <br />
                <center style="font-size: 2rem;font-family:sans-serif;">
                  <pre> S.I.I LOS ANGELES</pre>
                </center>
                <!-- <br /> -->
                <center style="font-size: 2rem;font-family:sans-serif;">
                  <pre> {{ pre_factura.emisor.empresa }}</pre>
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
                  <label class="upper"><b>DIRECCION: </b> {{pre_factura.emisor.direccion}}</label><br>
                  <label class="upper"><b>GIRO: </b>{{ pre_factura.emisor.giro }}</label> <br>


                  <b>EMISION&nbsp;: </b> {{ pre_factura.Fecha | moment("DD/MM/YYYY") }} <br />
                  <b class="upper">MEDIO DE PAGO&nbsp;: </b> {{ pre_factura.FormaPago_str }} <br /><br>
                  <b class="upper">SEÑOR(A)&nbsp;: </b> {{ pre_factura.Cliente.RazonSocial }} <br />
                  <b class="upper">RUT&nbsp;: </b> {{ pre_factura.Cliente.Rut }} <br />
                  <b class="upper">DIRECCION(A)&nbsp;: </b> {{ pre_factura.Cliente.Direccion }} <br />
                  <b class="upper">COMUNA(A)&nbsp;: </b> {{ pre_factura.Cliente.Comuna }} <br />
                  <b class="upper">CIUDAD(A)&nbsp;: </b> {{ pre_factura.Cliente.Ciudad }} <br />

                  <b class="upper">GIRO&nbsp;: </b> {{ pre_factura.Cliente.Giro }} <br />

                  <table style="width:100%; padding-right:2px;">
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

                    <tr v-for="c in pre_factura.Productos " :key="c.id">
                        <td style="width:20px">{{ c.NombreProducto  }}</td>
                        <td>{{ formatPrice(c.PrecioNeto) }}</td>
                        <td>{{ c.Cantidad }}</td>
                        <td>{{ c.UnidadMedida }}</td>
                        <!-- <td>{{ formatPrice(c.PrecioNeto * c.Cantidad) }}</td> -->
                        <td>{{ formatPrice(c.SubTotal) }}</td>
                    </tr>

                    <tr>
                      <td
                        class="fintabla"
                        style="
                          border-bottom: 1px solid black;
                          border-top: 1px solid black;
                        "
                        colspan="4"
                      >
                        <div style="text-align: right">TOTAL NETO&nbsp;:</div>
                      </td>
                      <td
                        class="fintabla"
                        style="
                          border-bottom: 1px solid black;
                          border-top: 1px solid black;
                        "
                      >
                      <label v-if="dte_precio=='neto'"> $ {{ formatPrice(suma_solo_ivas) }}</label>
                      <label v-if="dte_precio=='iva_incluido'"> $ {{ formatPrice(Math.round(suma_solo_ivas) / 1.19 ) }}</label>
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
                        colspan="4"
                      >
                        <div style="text-align: right">TOTAL EXENTO&nbsp;:</div>
                      </td>
                      <td
                        class="fintabla"
                        style="
                          border-bottom: 1px solid black;
                          border-top: 1px solid black;
                        "
                      >
                      <label v-if="dte_precio=='neto'"> $ {{ formatPrice(Math.round(suma_solo_exento)) }}</label>
                      <label v-if="dte_precio=='iva_incluido'"> $ {{ formatPrice(Math.round(suma_solo_exento) ) }}</label>
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
                        colspan="4"
                      >
                        <div style="text-align: right">IMP. ESPECIFICO&nbsp;:</div>
                      </td>
                      <td
                        class="fintabla"
                        style="
                          border-bottom: 1px solid black;
                          border-top: 1px solid black;
                        "
                      >
                      $ {{ formatPrice(impuesto_especifico) }}
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
                        colspan="4"
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
                       <label v-if="dte_precio=='neto'"> $ {{ formatPrice(((suma_solo_ivas * 119)/100) - suma_solo_ivas )}}</label>
                       <label v-if="dte_precio=='iva_incluido'"> $ {{ formatPrice(Math.round(suma_solo_ivas) - Math.round(suma_solo_ivas)/1.19 ) }}</label>
                      </td>
                    </tr>

                    <tr>
                      <td
                        class="fintabla"
                        style="
                          border-bottom: 1px solid black;
                          border-top: 1px solid black;
                        "
                        colspan="4"
                      >
                        <div style="text-align: right">MONTO BRUTO&nbsp;:</div>
                      </td>
                      <td
                        class="fintabla"
                        style="
                          border-bottom: 1px solid black;
                          border-top: 1px solid black;
                        "
                      >
                       <!-- $ {{formatPrice((pre_factura.venta_total * 119) / 100)}} -->
                       <label v-if="dte_precio=='neto'"> $ {{ formatPrice( redondeo(redon_medio_pago, total + impuesto_especifico + (((suma_solo_ivas * 119)/100) - suma_solo_ivas ) ) )}} </label>
                       <label v-if="dte_precio=='iva_incluido'"> $ {{ formatPrice(redondeo(redon_medio_pago,total + Number(impuesto_especifico) ) ) }}</label>
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
                  width="90%"
                  height="120%"
                  alt="Barcode Generator TEC-IT"
                  :src="'https://barcode.tec-it.com/barcode.ashx?data='+ted+'&code=PDF417&multiplebarcodes=false&translate-esc=false&unit=Fit&dpi=96&imagetype=Gif&rotation=0&color=%23000000&bgcolor=%23ffffff&codepage=Default&qunit=Mm&quiet=0'"
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

              <!-- FIN DEL DISEÑO DE LA FACTURACION ELECTRONICA -->
            </div>

        </div>
        <div class="modal-footer">

            <!-- <button @click="printDiv('pdfFactura')"></button> -->
            <button
              type="button"
              class="btn btn-primary"
              onclick="printJS({
          printable: 'pdfFactura',
          type:'html',
          style: 'b{text-transform: uppercase;},pre{text-transform: uppercase;font-family:sans-serif;width: 100%;white-space: pre-wrap;white-space: -moz-pre-wrap;white-space: -pre-wrap;white-space: -o-pre-wrap;word-wrap: break-word;}'
         })"
            >
              Imprimir
            </button>


            <button
              v-if="dte_precio=='neto'"
              type="button"
              class="btn btn-success"
              @click="emitir_dte33(
                  pre_factura,
                  /*total neto*/ suma_solo_ivas,
                  /* EXENTO */ Math.round(suma_solo_exento),
                  /*IMP. ESPECIFICO*/  impuesto_especifico,
                  /*I.V.A 19% :*/Math.round(((suma_solo_ivas * 119)/100) - suma_solo_ivas),
                  /*MONTO BRUTO*/ redondeo(redon_medio_pago,Math.round( total + impuesto_especifico + (((suma_solo_ivas * 119)/100) - suma_solo_ivas )  )),
                  /*(no visible en factura, 'vuelto')*/ Math.round(Number(montoEfectivo) + Number(montoDebito) -  /*TOTAL A PAGAR->*/redondeo(redon_medio_pago,total + impuesto_especifico + (((suma_solo_ivas * 119)/100) - suma_solo_ivas ))  ),

                  /* deuda, si es que existiera */ Math.round((total + impuesto_especifico + (((suma_solo_ivas * 119)/100) - suma_solo_ivas ))   -  ( Number(montoEfectivo) + Number(montoDebito)) ),
                  /*credito*/ ((Math.round((total + impuesto_especifico + (((suma_solo_ivas * 119)/100) - suma_solo_ivas ))   -  ( Number(montoEfectivo) + Number(montoDebito)) )) )

              )"
            >
              Emitir Dte
            </button>

            <button
              v-if="dte_precio=='iva_incluido'"
              type="button"
              class="btn btn-success"
              @click="emitir_dte33(
                  pre_factura,
                  /*total neto*/ Math.round(suma_solo_ivas / 1.19 ),
                  /* EXENTO */ Math.round(suma_solo_exento),
                  /*IMP. ESPECIFICO*/  impuesto_especifico,
                  /*I.V.A 19% :*/Math.round(suma_solo_ivas) - Math.round(suma_solo_ivas/1.19),
                  /*MONTO BRUTO*/ redondeo(redon_medio_pago,total + Number(impuesto_especifico) ),
                  /*(no visible en factura, 'vuelto')*/ (Math.round(Number(montoEfectivo) + Number(montoDebito)) -  /*TOTAL A PAGAR->*/(redondeo(redon_medio_pago, total + Number(impuesto_especifico) ))),

                  /* deuda, si es que existiera */  (redondeo(redon_medio_pago,total + Number(impuesto_especifico) ) ) -  ( Number(montoEfectivo) + Number(montoDebito)),

                   /*credito*/ (redondeo(redon_medio_pago,total + Number(impuesto_especifico) ) ) -  ( Number(montoEfectivo) + Number(montoDebito))

              )"
            >
              Emitir Dte
            </button>
        </div>

    </b-modal>






    <div class="row my-4 mx-1">
      <div class="col-12">
          <h4>Facturación electrónica </h4>
        <b-card class="text-center transparencia">

            <!-- DETALLE FACTURA ARRIBA -->
            <b-card>

                <div class="row text-left">

                <div class="col-md-8">
                    <div class="card">
                        <div class="card-body">
                    <h5>Datos del receptor (Cliente)</h5>
                    <div class="row">
                        <div class="col-md-12">
                            <label for="">Rut de la empresa o cliente:</label>
                         <b-input-group>
                            <input class="form-control form-control-sm" @keypress.enter="traer_rut_empresa()" id="rut" @blur="formatear_rut" placeholder="Buscar rut.." type="text">
                            <!-- :disabled="btn_buscar_rut" -->
                            <b-input-group-append>
                                <b-button
                                :disabled="btn_buscar_rut"
                                id="btnBuscar"
                                block
                                variant="success"
                                @click="traer_rut_empresa()"
                                size="sm"
                                >
                                    <i class="fas fa-search"></i>
                                </b-button>
                            </b-input-group-append>
                        </b-input-group>
                        </div>
                    </div>
                    <br>
                    <div class="row" v-if="ver_cliente">

                        <div class="col-md-6">
                             <div class="input-group input-group-sm mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text" id="basic-addon3">Rut: </span>
                                </div>
                                <input :disabled="true" type="text" v-model="mostrar_cliente.rut" class="form-control " id="basic-url" aria-describedby="basic-addon3">
                            </div>

                        </div>
                        <div class="col-md-6">

                             <div class="input-group input-group-sm mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text" id="basic-addon3">Razón social: </span>
                                </div>
                                <input :disabled="true" type="text" v-model="mostrar_cliente.cliente" class="form-control " id="basic-url" aria-describedby="basic-addon3">
                            </div>
                        </div>
                    </div>

                    <div class="row" v-if="ver_cliente">

                        <div class="col-md-6">

                             <div class="input-group input-group-sm mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text" id="basic-addon3">Dirección: </span>
                                </div>
                                <input type="text" v-model="mostrar_cliente.direccion" class="form-control " id="basic-url" aria-describedby="basic-addon3">
                            </div>
                        </div>
                        <div class="col-md-6">

                            <div class="input-group input-group-sm mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text" id="basic-addon3">Comuna: </span>
                                </div>
                                <input type="text" v-model="mostrar_cliente.comuna" class="form-control " id="basic-url" aria-describedby="basic-addon3">
                            </div>
                        </div>
                    </div>

                    <div class="row" v-if="ver_cliente">
                        <div class="col-md-6">

                             <div class="input-group input-group-sm mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text" id="basic-addon3">Ciudad: </span>
                                </div>
                                <input type="text" v-model="mostrar_cliente.ciudad" class="form-control " id="basic-url" aria-describedby="basic-addon3">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="input-group input-group-sm mb-3">
                            <div class="input-group-prepend">
                                <span class="input-group-text" id="basic-addon3">Contacto: </span>
                            </div>
                            <input type="text" v-model="mostrar_cliente.contacto" class="form-control form-control-sm " id="basic-url" aria-describedby="basic-addon3">
                          </div>
                        </div>
                    </div>

                    <div class="row" v-if="ver_cliente">
                        <div class="col-md-12">
                            <div class="input-group input-group-sm mb-3">
                            <div class="input-group-prepend">
                                <span class="input-group-text" id="basic-addon3">Giro: </span>
                            </div>
                            <input type="text" v-model="mostrar_cliente.giro" class="form-control " id="basic-url" aria-describedby="basic-addon3">
                          </div>
                        </div>
                    </div>

                    <br>
                    <h5>Datos generales (Cliente)</h5>
                    <label for="">Fecha de emisión:</label>
                    <!-- {{ date.fecha }} -->
                    <input v-model="date.date" class="form-control form-control-sm" type="date" name="" id="">
                    <br>
                      <label>Forma de pago:</label>
                     <br>
                      <select v-model="sii_forma_pago" @change="formaPago=[]; montoEfectivo='0'; montoDebito='0';redon_medio_pago='DEBITO'" name="" id="" class="form-control form-control-sm">
                           <option value="">--SELECCIONE--</option>
                          <option value="CONTADO">Contado</option>
                          <option value="CREDITO">Crédito</option>
                          <!-- <option value="SIN COSTO">Sin costo</option> -->
                      </select>
                    <br>
                    <b-form-group v-if="sii_forma_pago == 'CONTADO'" label="Tipo de pago:">

                    <b-form-checkbox-group
                      v-model="formaPago"
                      :options="formaPagoOpcion"
                      name="buttons-1"
                      button-variant="outline-success"
                      buttons
                      size="sm"
                      @input="david_kk"
                    ></b-form-checkbox-group>
                    <br>
                    <!-- {{redon_medio_pago}} -->
                    <div
                  v-if="
                    formaPago == '1' || formaPago == '1,2' || formaPago == '2,1'
                  "
                >
                  <b-form-group label="Monto en CLP">
                    <b-input-group>
                         <b-input-group-append>
                        <b-button size="sm" text="Button" disabled
                          >Efectivo</b-button
                        >
                      </b-input-group-append>
                      <b-form-input
                        size="sm"
                        type="number"
                        placeholder="Ingrese el monto que cancela el cliente"
                        v-model="montoEfectivo"
                        v-on:keyup="cliente_paga = Math.trunc((total * 119)/100) + Number(impuesto_especifico) - (montoEfectivo + montoDebito)"
                        >{{ formatPrice() }}</b-form-input
                      >

                    </b-input-group>
                  </b-form-group>
                </div>

                <div
                  v-if="
                    formaPago == '2' || formaPago == '1,2' || formaPago == '2,1'
                  "
                >
                  <b-form-group label="Monto en CLP">
                    <b-input-group>
                        <b-input-group-append>
                        <b-button size="sm" text="Button" disabled
                          >T.Debito</b-button
                        >
                      </b-input-group-append>
                      <b-form-input
                        size="sm"
                        type="number"
                        placeholder="Ingrese el monto que cancela el cliente"
                        v-model="montoDebito"
                      ></b-form-input>

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
                        <b-button size="sm" text="Button" disabled
                          >T.Credito</b-button
                        >
                      </b-input-group-append>
                    </b-input-group>
                  </b-form-group>
                </div>
                  </b-form-group>

                  <div v-if="sii_forma_pago == 'CREDITO'">
                      <br>
                        <textarea v-model="detalle_credito" class="form-control" style="resize: none;" placeholder="Detalle de la deuda(opcional).." name="" id="" cols="30" rows="2"></textarea>
                        <br>
                        <!-- <input v-model="monto_credito" class="form-control form-control-sm" type="numeric" placeholder="Monto de la deuda.."> -->

                        <!-- pegar aca -->

                        <!-- CREDITO CON EFECTIVO -->
                        <b-input-group>
                            <b-input-group-append>
                                <b-button size="sm" text="Button" disabled
                                >Efectivo</b-button
                                >
                            </b-input-group-append>
                            <b-form-input
                                size="sm"
                                type="number"
                                placeholder="Abonar en efectivo"
                                v-model="montoEfectivo"
                                ></b-form-input
                            >

                            </b-input-group>
                            <br>
                            <!-- CREDITO CON DEBITO -->
                              <b-input-group>
                            <b-input-group-append>
                                <b-button size="sm" text="Button" disabled
                                >Debito</b-button
                                >
                            </b-input-group-append>
                            <b-form-input
                                size="sm"
                                type="number"
                                placeholder="Abonar en debito"
                                v-model="montoDebito"
                                >{{ formatPrice() }}</b-form-input
                            >

                            </b-input-group>
                        <!-- <input v-model="montoDebito" class="form-control form-control-sm" type="numeric" placeholder="Abono en debito"> -->
                  </div>
                </div>
                </div>
                </div>
                <div class="col-md-4">
                <div class="card" style="background:#E5E8E8">
                    <div class="card-body">
                            <h5>Datos de la venta</h5>
                    <div class="row">
                        <div class="col-md-12">
                            <b-button
                                    :disabled="visualizar_compra"
                                    pill
                                    block
                                    size="sm"
                                    id="show-btn"
                                    class="my-2"
                                    variant="info"
                                    @click="visualizar_factura(mostrar_cliente)"
                                    ><i class="far fa-eye"></i> Previsualizar y generar factura</b-button>
<!--
                            <button @click="dte_34">
                                DTE 34 exento (test)</button> -->

                                <hr>
                        </div>
                    </div>

                  <!-- detalle venta -->
<!-- esta parte es solo para precio NETO -->
<div v-if="dte_precio == 'neto'">
                  <label>
                    <b>Detalle de Venta</b>
                  </label>
                  <div class="row">
                    <div class="col-8">
                      <label>Total neto (afecto)</label>
                    </div>
                    <div class="col-4">
                      <label>$ {{ formatPrice(suma_solo_ivas) }}</label>
                    </div>

                    <div class="col-8">
                      <label>Exento</label>
                    </div>
                    <div class="col-4">
                      <label>$ {{ formatPrice(suma_solo_exento) }}</label>
                    </div>



                    <div class="col-8">
                      <label>Impuesto especifico</label>
                    </div>
                    <div class="col-4">
                      <label>$ {{ formatPrice(impuesto_especifico) }}</label>
                    </div>
                    <div class="col-8">
                        <label for="">(I.V.A 19%)</label>
                    </div>
                    <div class="col-4">
                        <label>$ {{ formatPrice(((suma_solo_ivas * 119)/100) - suma_solo_ivas )}}</label>

                    </div>
                    <div class="col-8">
                      <label>Total a Pagar</label>
                    </div>
                    <div class="col-4">


                      <label><b>$ {{ formatPrice( redondeo(redon_medio_pago,total + impuesto_especifico + (((suma_solo_ivas * 119)/100) - suma_solo_ivas ))  )}}</b></label>

                    </div>

                    <div class="col-8">
                        <hr>
                      <label>Cliente Paga</label>
                    </div>
                    <div class="col-4">


                      <div >
                          <hr>
                        <label>$ {{ formatPrice(Number(montoEfectivo) + Number(montoDebito) ) }}</label>
                      </div>

                    </div>


                    <div class="col-8" v-if="sii_forma_pago == 'CREDITO'">
                       <label for="">Deuda (credito)</label>

                    </div>

                     <div class="col-4">

                      <div v-if="sii_forma_pago == 'CREDITO'">

                        <label>$ {{

                             formatPrice((Math.round((total + impuesto_especifico + (((suma_solo_ivas * 119)/100) - suma_solo_ivas ))   -  ( Number(montoEfectivo) + Number(montoDebito)) )) )

                             }}
                        </label>
                      </div>

                    </div>



                  </div>
                    <div class="row">
                        <div class="col-8" v-if="sii_forma_pago == 'CONTADO'">
                        <label>Vuelto</label>
                        </div>
                        <div class="col-4">

                        <div v-if="sii_forma_pago == 'CONTADO'">

                            <label>$ {{
                                (Number(montoEfectivo) + Number(montoDebito))?
                                /*formatPrice( ((Number(montoEfectivo) + Number(montoDebito)) - Math.trunc(((total * 119)/100) + Number(impuesto_especifico)  ) ))*/
                                formatPrice((Math.round(Number(montoEfectivo) + Number(montoDebito) -  /*TOTAL A PAGAR->*/redondeo(redon_medio_pago,total + impuesto_especifico + (((suma_solo_ivas * 119)/100) - suma_solo_ivas ))  ) ))
                                :0
                                }}
                            </label>
                        </div>


                        </div>
                    </div>
                  <!-- detalle venta -->
</div>
<!-- FIN esta parte es solo para precio NETO -->

<!-- ESTA PARTE ES SOLO PARA PRECIO + IVA -->
<div v-if="dte_precio == 'iva_incluido'">
    <label> <b>Detalle de Venta</b> </label>
        <div class="row">


            <div class="col-8">
                <label>Monto neto (afecto)</label>
            </div>
             <div class="col-4">
                 $ {{ formatPrice(Math.round(suma_solo_ivas) / 1.19 ) }}
             </div>

             <div class="col-8">
                <label>Exento</label>
            </div>
             <div class="col-4">
                 $ {{ formatPrice(Math.round(suma_solo_exento) /*/ 1.19*/ ) }}
             </div>

             <div class="col-8">
                      <label>Impuesto especifico</label>
                    </div>
                    <div class="col-4">
                      <label>$ {{ formatPrice(impuesto_especifico) }}</label>
             </div>

             <div class="col-8">
                <label>(I.V.A 19%)</label>
            </div>
             <div class="col-4">
                 $ {{ formatPrice(Math.round(suma_solo_ivas) - Math.round(suma_solo_ivas)/1.19 ) }}
             </div>

            <!-- TOTAL A PAGAR -->
            <div class="col-8">
                <label>Total a pagar</label>
            </div>
             <div class="col-4"><b>
                 $ {{ formatPrice(redondeo(redon_medio_pago,total + Number(impuesto_especifico) ) ) }}
             </b></div>
        </div>


        <div class="row">
            <div class="col-8">
                <hr>
                <label>Cliente Paga</label>
            </div>
            <div class="col-4">


                <div >
                    <hr>
                    <label>$ {{ formatPrice(Number(montoEfectivo) + Number(montoDebito) ) }}</label>
                </div>

            </div>


            <div class="col-8" v-if="sii_forma_pago == 'CREDITO'">
                <label for="">Deuda (credito)</label>

            </div>

            <div class="col-4">

                <div v-if="sii_forma_pago == 'CREDITO'">

                    <label>$ {{


                        formatPrice(Math.round( (total + Number(impuesto_especifico)) - ( Number(montoEfectivo) + Number(montoDebito))))

                        }}
                    </label>
                </div>





             </div>


         </div>

         <div class="row">
                 <div class="col-8" v-if="sii_forma_pago == 'CONTADO'">
                        <label>Vuelto</label>
              </div>
              <div class="col-4">

                        <div v-if="sii_forma_pago == 'CONTADO'">


                            <label>$ {{
                                (Number(montoEfectivo) + Number(montoDebito))?
                                /*formatPrice( ((Number(montoEfectivo) + Number(montoDebito)) - Math.trunc(((total * 119)/100) + Number(impuesto_especifico)  ) ))*/
                                formatPrice(( (Math.round(Number(montoEfectivo) + Number(montoDebito)) -  /*TOTAL A PAGAR->*/(redondeo(redon_medio_pago, total + Number(impuesto_especifico) )))  ) )
                                :0
                                }}
                            </label>
                        </div>

                </div>


            </div>

</div>

<!-- FIN ESTA PARTE ES SOLO PARA PRECIO + IVA -->


                  <!-- Comprobante -->
                  <div class="row justify-content-end">
                    <div class="col-12">
                      <!-- BOTON VENTAS -->
                      <div>

                        <button id="btn_ultima_venta" :disabled="traer_ul_venta" @click="abrir_ultima_venta('comprobante',local_storage_venta)" v-if="local_storage_venta !=''" class="btn btn-link">Traer mi ultima venta (ID: {{local_storage_venta}})</button>

                        <!-- modal ultima factura de venta (factura electronica) -->
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


                        <section >

                                <div id="pdfFactura">
                                <!-- AQUI EL DISEÑO DE LA FACTURA ELECTRONICA -->
                                <div class="factura">
                                    <!-- <center id="centro">soy centro</center> -->
                                    <center id="centro_bordes">
                                    <b class="upper"><pre id="texto_color" >R.U.T {{ emisor.rut }}</pre></b>
                                    <b><pre id="texto_color">FACTURA ELECTRONICA</pre></b>
                                    <b><pre id="texto_color">Nº {{fac_venta.folio}}</pre></b>
                                    </center>
                                    <br />
                                    <center style="font-size: 2rem;font-family:sans-serif;">
                                    <pre> S.I.I LOS ANGELES</pre>
                                    </center>
                                    <!-- <br /> -->
                                    <center style="font-size: 2rem;font-family:sans-serif;">
                                    <pre> {{ emisor.empresa }}</pre>
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

                                    <label class="upper"><b>DIRECCION: </b> {{emisor.direccion}}</label><br>
                                    <label class="upper"><b>GIRO: </b>{{ emisor.giro }}</label> <br>


                                    <b>EMISION&nbsp;: </b> {{ post_factura.Fecha | moment("DD/MM/YYYY") }} <br />
                                    <b class="upper">MEDIO DE PAGO&nbsp;: </b> {{ post_factura.FormaPago_str }} <br /><br>
                                    <b class="upper">SEÑOR(A)&nbsp;: </b> {{ fac_cliente.RazonSocial }} <br />
                                    <b class="upper">RUT&nbsp;: </b> {{ fac_cliente.Rut }} <br />
                                    <b class="upper">DIRECCION(A)&nbsp;: </b> {{ fac_cliente.Direccion }} <br />
                                    <b class="upper">COMUNA(A)&nbsp;: </b> {{ fac_cliente.Comuna }} <br />
                                    <b class="upper">CIUDAD(A)&nbsp;: </b> {{ fac_cliente.Ciudad }} <br />

                                    <b class="upper">GIRO&nbsp;: </b> {{ fac_cliente.Giro }} <br />

                                    <table style="width:100%; padding-right:2px;">
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

                                        <tr v-for="c in ticketPrintDetalle " :key="c.id">
                                            <td>{{ c.nombre  }}</td>
                                            <td>{{ formatPrice(c.precio) }}</td>
                                            <td>{{ c.cantidad }}</td>
                                            <td>{{ c.unidad }}</td>
                                            <!-- <td>{{ formatPrice(c.PrecioNeto * c.Cantidad) }}</td> -->
                                            <td>{{ formatPrice(c.precio) }}</td>
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

                                        <label v-if="dte_precio=='neto'"> $ {{ formatPrice(fac_venta.totales_neto) }}</label>
                                        <label v-if="dte_precio=='iva_incluido'"> $ {{ formatPrice(Math.round(fac_venta.totales_neto) ) }}</label>
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
                                            <div style="text-align: right">TOTAL EXENTO&nbsp;:</div>
                                        </td>
                                        <td
                                        colspan="2"
                                            class="fintabla"
                                            style="
                                            border-bottom: 1px solid black;
                                            border-top: 1px solid black;
                                            "
                                        >
                                        <label v-if="dte_precio=='neto'"> $ {{ formatPrice(Math.round(fac_venta.totales_exento)) }}</label>
                                        <label v-if="dte_precio=='iva_incluido'"> $ {{ formatPrice(Math.round(fac_venta.totales_exento) ) }}</label>
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
                                        $ {{ formatPrice(fac_venta.totales_impuesto_especifico) }}
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
                                            colspan="2"
                                            class="fintabla"
                                            style="
                                            border-bottom: 1px solid black;
                                            border-top: 1px solid black;
                                            "
                                        >
                                            <!-- $ {{ formatPrice(((pre_factura.venta_total * 119) / 100) - pre_factura.venta_total  ) }} -->
                                        <label v-if="dte_precio=='neto'"> $ {{ formatPrice(fac_venta.totales_iva )}}</label>
                                        <label v-if="dte_precio=='iva_incluido'"> $ {{ formatPrice(fac_venta.totales_iva) }}</label>
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
                                        <label v-if="dte_precio=='neto'"> $ {{ formatPrice( fac_venta.venta_total )}} </label>
                                        <label v-if="dte_precio=='iva_incluido'"> $ {{ formatPrice(fac_venta.venta_total ) }}</label>
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
                                    :src="'https://barcode.tec-it.com/barcode.ashx?data='+fac_venta.ted+'&code=PDF417&multiplebarcodes=false&translate-esc=false&unit=Fit&dpi=96&imagetype=Gif&rotation=0&color=%23000000&bgcolor=%23ffffff&codepage=Default&qunit=Mm&quiet=0'"
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

                                <!-- FIN DEL DISEÑO DE LA FACTURACION ELECTRONICA -->
                                </div>


                                <div class="modal-footer">

                                    <!-- <button @click="printDiv('pdfFactura')"></button> -->
                                    <button
                                    type="button"
                                    class="btn btn-primary"
                                    onclick="printJS({
                                printable: 'pdfFactura',
                                type:'html',
                                style:'#texto_color{color:black;}#centro_bordes{font-size: 2rem; border:3px solid black;color:black;font-family:sans-serif;}#centro{color:black};b{text-transform: uppercase;color:black;},pre{text-transform: uppercase;font-family:sans-serif;width: 100%;white-space: pre-wrap;white-space: -moz-pre-wrap;white-space: -pre-wrap;white-space: -o-pre-wrap;word-wrap: break-word;color:black;}'
                                })"
                                    >
                                    Imprimir DTE
                                    </button>


                                    <!-- <button
                                    v-if="dte_precio=='neto'"
                                    type="button"
                                    class="btn btn-success"
                                    @click="emitir_dte33(
                                        pre_factura,
                                        /*total neto*/ suma_solo_ivas,
                                        /* EXENTO */ Math.round(suma_solo_exento),
                                        /*IMP. ESPECIFICO*/  impuesto_especifico,
                                        /*I.V.A 19% :*/Math.round(((suma_solo_ivas * 119)/100) - suma_solo_ivas),
                                        /*MONTO BRUTO*/ redondeo(redon_medio_pago,Math.round( total + impuesto_especifico + (((suma_solo_ivas * 119)/100) - suma_solo_ivas )  )),
                                        /*(no visible en factura, 'vuelto')*/ Math.round(Number(montoEfectivo) + Number(montoDebito) -  /*TOTAL A PAGAR->*/redondeo(redon_medio_pago,total + impuesto_especifico + (((suma_solo_ivas * 119)/100) - suma_solo_ivas ))  ),

                                        /* deuda, si es que existiera */ Math.round((total + impuesto_especifico + (((suma_solo_ivas * 119)/100) - suma_solo_ivas ))   -  ( Number(montoEfectivo) + Number(montoDebito)) ),
                                        /*credito*/ ((Math.round((total + impuesto_especifico + (((suma_solo_ivas * 119)/100) - suma_solo_ivas ))   -  ( Number(montoEfectivo) + Number(montoDebito)) )) )

                                    )"
                                    >
                                    Emitir Dte
                                    </button> -->

                                    <!-- <button
                                    v-if="dte_precio=='iva_incluido'"
                                    type="button"
                                    class="btn btn-success"
                                    @click="emitir_dte33(
                                        pre_factura,
                                        /*total neto*/ Math.round(suma_solo_ivas / 1.19 ),
                                        /* EXENTO */ Math.round(suma_solo_exento),
                                        /*IMP. ESPECIFICO*/  impuesto_especifico,
                                        /*I.V.A 19% :*/Math.round(suma_solo_ivas) - Math.round(suma_solo_ivas/1.19),
                                        /*MONTO BRUTO*/ redondeo(redon_medio_pago,total + Number(impuesto_especifico) ),
                                        /*(no visible en factura, 'vuelto')*/ (Math.round(Number(montoEfectivo) + Number(montoDebito)) -  /*TOTAL A PAGAR->*/(redondeo(redon_medio_pago, total + Number(impuesto_especifico) ))),

                                        /* deuda, si es que existiera */  (redondeo(redon_medio_pago,total + Number(impuesto_especifico) ) ) -  ( Number(montoEfectivo) + Number(montoDebito)),

                                        /*credito*/ (redondeo(redon_medio_pago,total + Number(impuesto_especifico) ) ) -  ( Number(montoEfectivo) + Number(montoDebito))

                                    )"
                                    >
                                    Emitir Dte
                                    </button> -->
                                </div>
                            </section>

                        </b-modal>
                      </div>
                      <!-- MODAL VENTAS  -->
                      <template>
                        <div>

                        </div>
                      </template>
                    </div>
                  </div>
                  <!-- comprobante -->
                    </div>
                    </div>
                </div>
            </div>
            </b-card>
        <br>
          <div class="row">
            <!-- buscar por codigo -->
            <div class="col-12 col-md-12 col-lg-12">
              <b-card class="largoCard">
                <div class="row">
                  <div class="col-12">
                    <div class="row">
                      <div class="col-md-12">
                        <input
                          placeholder="Buscar por nombre o descripcion de producto..."
                          class="form-control form-control-sm"
                          v-model="buscando_txt"
                          type="text"
                          v-on:keyup="buscando_personalizado"
                        />

                        <div
                          v-if="view_buscando"
                          style="
                            border: 1px solid #bfc9ca;
                            border-radius: 4px;
                            height: 200px;
                            overflow: scroll;
                          "
                        >
                          <table class="table">
                            <tr
                              v-for="data in lista_buscando"
                              :key="data.id"
                              class="row"
                            >
                              <td>
                                <b-img
                                 @click="getData(data.sku)"
                                  class="tamanio"
                                  thumbnail
                                  v-if="data.imagen"
                                  :src="data.imagen"
                                  alt="Image 1"
                                ></b-img>
                              </td>
                              <td>
                                <button
                                  @click="getData(data.sku)"
                                  type="button"
                                  class="btn btn-link"
                                >
                                  {{ data.nombre }}
                                </button>
                              </td>
                            </tr>
                          </table>
                        </div>
                      </div>
                    </div>
                    <br />
                    <b-input-group>
                      <b-form-input
                        ref="inputBuscar"
                        id="inputBuscar"
                        v-on:keyup="escribiendoProducto"
                        size="sm"
                        placeholder="Escanee el producto o ingrese el código de barras SKU"
                        v-model="buscadorProducto"
                        v-on:keyup.enter="traer_producto();"
                      ></b-form-input>
                      <b-input-group-append>
                        <b-button
                          id="btnBuscar"
                          :disabled="btn_buscar_producto"
                          block
                          variant="success"
                          @click="traer_producto()"
                          size="sm"
                          >Buscar</b-button
                        >
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
                        <template v-slot:cell(sku)="data">{{
                          data.item.sku
                        }}</template>
                        <template v-slot:cell(cantidad)="data">
                          <input
                            name="input_cantidad"
                            @input="
                              ingresar_cantidad_carro(
                                data.index,
                                $event.target.value
                              )
                            "
                            @click="
                              ingresar_cantidad_carro(
                                data.index,
                                $event.target.value
                              )
                            "
                            class="form-control form-control-sm"
                            :value="data.item.cantidad_ls"
                          />
                        </template>

                        <template v-slot:cell(unidad)="data" >
                            <input
                            name="input_unidad"
                            @input="
                              ingresar_unidad_carro(
                                data.index,
                                $event.target.value
                              )
                            "
                            @click="
                              ingresar_unidad_carro(
                                data.index,
                                $event.target.value
                              )
                            "
                            class="form-control form-control-sm"
                             :value="data.item.unidad"
                          />
                        </template>

                        <template v-slot:cell(producto)="data">
                          <b>{{ data.item.nombre }}</b>
                          <br />
                          <em
                            >{{ formatPrice(data.item.cantidad) }} unidades
                            disponibles</em
                          >
                        </template>
                        <template v-slot:cell(afecto)="data">
                            <select :value="(data.item.afecto)?data.item.afecto:'true'"
                                @change="ingresar_afecto_carro(
                                    data.index,
                                    $event.target.value
                                ) "
                                name="input_afecto"
                                class="form-control form-control-sm">
                                    <option value="true">SI</option>
                                    <option value="false">NO</option>
                            </select>

                            <!-- {{(data.item.afecto=='false')?false:true}} -->
                        </template>
                        <template v-slot:cell(tia)="data">
                            <!-- {{ (data.item.afecto) }} -->
                            <!-- :disabled="(data.item.afecto=='false')?true:false" -->
                            <select   :value="(data.item.tipo_impuesto_adicional)?data.item.tipo_impuesto_adicional:'0'"
                                @change="ingresar_tipo_imp_adic_carro(
                                    data.index,
                                    $event.target.value,
                                ) "
                                name="input_tipo_impuesto_adicional"
                                class="form-control form-control-sm">
                                    <option v-for ="x in tipos_imp_adicionales" :key="x.id" :value="x.id">{{x.nombre}}</option>

                            </select>
                        </template>
                        <template v-slot:cell(mia)="data">
                            <!-- :disabled="(data.item.afecto=='false')?true:false" -->
                            <input
                            @click="ingresar_mia_carro(data.index, $event.target.value)"
                            @input="ingresar_mia_carro(data.index, $event.target.value)"
                             :value="data.item.monto_impuesto_adicional" class="form-control form-control-sm" type="text" name="input_monto_impuesto_adicional" >
                        </template>
                        <template v-slot:cell(precioProd)="data"
                          >$ {{ formatPrice(data.item.precio) }}</template>

                         <template v-slot:cell(descuento)="data">
                            <input type="number"
                            name="input_descuento"
                            @input="
                              ingresar_descuento_carro(
                                data.index,
                                $event.target.value
                              )
                            "
                            @click="
                              ingresar_descuento_carro(
                                data.index,
                                $event.target.value
                              )
                            "
                            class="form-control form-control-sm"
                            :value="data.item.descuento"
                          />
                        </template>

                        <template v-slot:cell(subtotal)="data"
                          >$
                          {{
                            formatPrice(
                                //aplicar descuento
                                //   sub total - ((subtotal) * descuento / 100) //math round redondeo para atras -5
                              (data.item.precio * data.item.cantidad_ls) - Math.round((data.item.precio * data.item.cantidad_ls) * ((data.item.descuento)?data.item.descuento:0) / 100)
                            )
                          }}</template
                        >
                        <template v-slot:cell(opc)="data">
                          <div class="col-12 col-xl-12">
                            <b-button
                              size="sm"
                              pill
                              variant="danger"
                              @click="eliminarItem(data.index)"
                              >x</b-button
                            >
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
                        >Quitar todo</b-button
                      >
                    </div>

                    <div class="col-4">
                      <label>
                        Total:
                        <b>$ {{ formatPrice(total) }}</b>
                      </label>
                    </div>
                  </div>
                  <!-- Total Temporal -->
                </template>
              </b-card>
            </div>
            <!-- buscar por codigo -->





          </div>

          <!-- SUCCESS VENTA -->
          <div class="col-12 mt-4">
            <ul>
              <b-alert
                variant="success"
                :show="dismissCountDown3"
                @dismissed="dismissCountDown3 = 0"
                @dismiss-count-down="countDownChanged3"
              >
                <p>{{ correcto3 }} {{ dismissCountDown3 }} segundos...</p>
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
                @dismissed="dismissCountDown4 = 0"
                @dismiss-count-down="countDownChanged4"
              >
                <p>{{ errores4 }} {{ dismissCountDown4 }} segundos...</p>
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
                @dismissed="dismissCountDown6 = 0"
                @dismiss-count-down="countDownChanged6"
              >
                <p>{{ errores6 }} {{ dismissCountDown6 }} segundos...</p>
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
              <b-alert v-if="false"
                variant="warning"
                :show="dismissCountDown5"
                @dismissed="dismissCountDown5 = 0"
                @dismiss-count-down="countDownChanged5"
              >
                <p>
                  El producto ya fue agregado al carro, modifique la cantidad en
                  la tabla. {{ dismissCountDown5 }} segundos...
                </p>
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


<script src="../dte_33/generar_dte_33.js"></script>
<style scoped src="../dte_33/genera_dte_33.css"></style>
<style src="vue-multiselect/dist/vue-multiselect.min.css"></style>

<style>
.avatar_indexx {
  height: 135px;
  width: 135px;
  border-radius: 30px;
}

/* Móviles en horizontal o tablets en vertical */
/* ------------------------------------------------------------------------- / */
@media (min-width: 768px) {
  .tamanio {
    max-width: 30%;
    max-height: 30%;
  }
}

/* / Tablets en horizonal y escritorios normales
   ------------------------------------------------------------------------- / */
/* @media (min-width: 1024px) {
    .tamanio{
        max-width: 50%;
        max-height: 50%;
    }
} */

/* / Escritorios muy anchos
   ------------------------------------------------------------------------- */
/* @media (min-width: 1200px) {
    .tamanio{
        max-width: 150px;
        max-height: 150px;
    }
} */

/*
  ##Device = Desktops
  ##Screen = 1281px to higher resolution desktops
*/

@media (min-width: 1281px) {
  .tamanio {
    max-width: 150px;
    max-height: 150px;
  }
}

/*
  ##Device = Laptops, Desktops
  ##Screen = B/w 1025px to 1280px
*/

@media (min-width: 1025px) and (max-width: 1280px) {
  .tamanio {
    max-width: 150px;
    max-height: 150px;
  }
}
.upper{
    text-transform: uppercase;
}

#centro{
    color:red;
}

#centro_bordes{
    font-size: 2rem; border:3px solid red;color:red;font-family:sans-serif;width:100%;
}
#texto_color{
    color:red;
}
</style>
