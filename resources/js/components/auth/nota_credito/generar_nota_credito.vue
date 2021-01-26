<template>
    <div v-if="usuario.rol==admin">
        <div class="container-fluid mt-4">

            <b-card class="text-center tituloTabla transparencia mb-4">
            <b-card-header class="fondoProductoAdm">GENERAR NOTA DE CREDITO</b-card-header>

            <b-container class="fondoTotal  col-12 m-2">

                <h5>Referencia al documento</h5>
                <div class="row g-3 align-items-center">
                    <div class="col-auto">
                        <label for="inputPassword6" class="col-form-label">Folio</label>
                    </div>
                    <div class="col-auto">
                        <input v-model="folio" type="number" id="inputPassword6" class="form-control form-control-sm" aria-describedby="passwordHelpInline">
                    </div>

                    <div class="col-auto">
                        <label for="inputPassword6" class="col-form-label">Documento</label>
                    </div>
                    <div class="col-auto">
                       <select v-model="documento" name="" id="" class="form-control form-control-sm">
                           <option value="">--SELECCIONE--</option>
                           <option value="33">33 - Factura electrónica</option>
                           <option value="39">39 - Boleta electrónica</option>
                           <option value="60">60 - Nota de crédito</option>
                       </select>
                    </div>
                    <div class="col-auto">
                        <span id="passwordHelpInline" class="form-text">
                        Fecha emisión
                        </span>
                    </div>
                    <div class="col-auto">
                        <input v-model="emision" type="date" class="form-control form-control-sm">
                    </div>

                    <div class="col-auto">
                        <label for="inputPassword6" class="col-form-label">Razón referencia</label>
                    </div>
                    <div class="col-auto">
                       <select v-model="razon" name="" id="" class="form-control form-control-sm">
                           <option value="">--SELECCIONE--</option>
                           <option value="Anular documento de referencia">Anular documento de referencia</option>
                           <option value="Corrige texto de referencia">Corregir texto de referencia</option>
                           <option value="Corrige montos"><i class="fas fa-search"></i>Corrige montos</option>
                       </select>
                    </div>
                </div>

                <div class="row justify-content-center">
                    <div class="col-md-3">
                        <button @click="venta_por_referencia_nc" class="m-4 btn btn-success" ><i class="fas fa-search"></i> Buscar</button>
                    </div>
                </div>


                <!--  AQUI SE RENDERIZA LA BUSQUEDA DE FOLIO/EMISION, LES LA TABLITA -->
                <b-modal
                    no-close-on-esc
                    no-close-on-backdrop
                    class="modal-header-ventas"
                    id="modal-ventas"
                    size="xl"
                    :ref="'modal_tabla'"
                    hide-footer
                    centered
                >
                    <div>
                        <h4>Referencias encontradas</h4>
                        <!-- <pre>{{tabla}}</pre> -->
                        <table class="table table-bordered">
                            <tr>
                                <td>ID</td>
                                <td>Doc.</td>
                                <td>Folio</td>
                                <td>Venta total</td>
                                <td>Emisión</td>
                                <td>Opción</td>
                            </tr>
                            <tr v-for="t in tabla" :key="t.id">
                                <td>{{ t.id }}</td>
                                <td>{{ t.dte }}</td>
                                <td>{{ t.folio }}</td>
                                <td>{{ t.venta_total }}</td>
                                <td>{{ t.emision_cl }}</td>
                                <td><button class="btn btn-link" @click="traer_referencia(t)">Seleccionar</button></td>
                            </tr>
                        </table>
                    </div>
                </b-modal>

                <!-- DATOS A MANEJAR PARA NOTA DE CREDITO -->
                <div  v-if="show_datos">
                    <div class="row">
                        <div class="col-md-9">
                            <label for="">Formato de precio:</label>
                            <select v-model="dte_precio" class="form-control" name="" id="">
                                <option  value="iva_incluido">Precio + iva incluido</option>
                                <!-- <option  value="neto">Precio neto</option> -->
                            </select>
                            <br>
                            <h4>Datos del receptor (cliente)</h4>
                            <br>
                            <div class="row" v-if="ver_cliente">


                                <div class="col-md-6">
                                    <div class="input-group input-group-sm mb-3">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text" id="basic-addon3">Rut: </span>
                                        </div>
                                        <input :disabled="true" type="text" v-model="datos.factura.Cliente.Rut" class="form-control " id="basic-url" aria-describedby="basic-addon3">
                                    </div>

                                </div>
                                <div class="col-md-6">

                                    <div class="input-group input-group-sm mb-3">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text" id="basic-addon3">Razón social: </span>
                                        </div>
                                        <input :disabled="true" type="text" v-model="datos.factura.Cliente.RazonSocial" class="form-control " id="basic-url" aria-describedby="basic-addon3">
                                    </div>
                                </div>
                            </div>

                            <div class="row" v-if="ver_cliente">

                                <div class="col-md-6">

                                    <div class="input-group input-group-sm mb-3">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text" id="basic-addon3">Dirección: </span>
                                        </div>
                                        <input :disabled="desactivar" type="text" v-model="datos.factura.Cliente.Direccion" class="form-control " id="basic-url" aria-describedby="basic-addon3">
                                    </div>
                                </div>
                                <div class="col-md-6">

                                    <div class="input-group input-group-sm mb-3">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text" id="basic-addon3">Comuna: </span>
                                        </div>
                                        <input :disabled="desactivar" type="text" v-model="datos.factura.Cliente.Comuna" class="form-control " id="basic-url" aria-describedby="basic-addon3">
                                    </div>
                                </div>
                            </div>

                            <div class="row" v-if="ver_cliente">
                                <div class="col-md-6">

                                    <div class="input-group input-group-sm mb-3">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text" id="basic-addon3">Ciudad: </span>
                                        </div>
                                        <input :disabled="desactivar" type="text" v-model="datos.factura.Cliente.Ciudad" class="form-control " id="basic-url" aria-describedby="basic-addon3">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="input-group input-group-sm mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text" id="basic-addon3">Contacto: </span>
                                    </div>
                                    <input :disabled="desactivar" type="text" v-model="datos.factura.Cliente.Contacto" class="form-control form-control-sm " id="basic-url" aria-describedby="basic-addon3">
                                </div>
                                </div>
                            </div>

                            <div class="row" v-if="ver_cliente">
                                <div class="col-md-12">
                                    <div class="input-group input-group-sm mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text" id="basic-addon3">Giro: </span>
                                    </div>
                                    <input :disabled="desactivar" type="text" v-model="datos.factura.Cliente.Giro" class="form-control " id="basic-url" aria-describedby="basic-addon3">
                                </div>
                                </div>
                            </div>

                            <div>
                                <br>
                                <h5>Datos generales (Cliente)</h5>
                                <label for="">Fecha de emisión:</label>
                                <!-- {{ date.fecha }} -->
                                <input :disabled="desactivar" v-model="date.date" class="form-control form-control-sm" type="date" name="" id="">
                                <br>
                                <label>Forma de pago:</label>
                                <br>
                                <select :disabled="desactivar" v-model="sii_forma_pago" @change="formaPago=[]; montoEfectivo='0'; montoDebito='0';redon_medio_pago='DEBITO'" name="" id="" class="form-control form-control-sm">
                                    <option value="">--SELECCIONE--</option>
                                    <option value="CONTADO">Contado</option>
                                    <option value="CREDITO">Crédito</option>
                                    <!-- <option value="SIN COSTO">Sin costo</option> -->
                                </select>
                                <br>
                                 <div v-if="!desactivar">
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
                                 </div>
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

                        <div class="col-md-3">
                            <div class="card" style="background:#E5E8E8">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <b-button
                                                    :disabled="visualizar_nc"
                                                    pill
                                                    block
                                                    size="sm"
                                                    id="show-btn"
                                                    class="my-2"
                                                    variant="info"
                                                    @click="visualizar_nota_credito(datos)"
                                                    ><i class="far fa-eye"></i> Previsualizar y generar nota de crédito</b-button>
                <!--
                                            <button @click="dte_34">
                                                DTE 34 exento (test)</button> -->

                                                <hr>

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
                                            <label>
                                                Monto bruto
                                            </label>
                                        </div>
                                        <div class="col-4">
                                            <b v-if="razon == 'Anular documento de referencia'">
                                            $ {{ formatPrice(datos.venta.venta_total) }}
                                            </b>
                                            <b v-if="razon == 'Corrige texto de referencia'">
                                            $ {{ formatPrice( ) }}
                                            </b>

                                            <b v-if="razon == 'Corrige montos'">
                                            $ {{ formatPrice(redondeo(redon_medio_pago,total + Number(impuesto_especifico) ) ) }}
                                            </b>
                                        </div>
                                    </div>

                                    <!-- que este false el desactivar -->
                                    <div class="row" v-if="!desactivar">
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

                                    <div v-if="!desactivar" class="row">
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
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>


                    <div class="col-12 my-4">
                    <div>
                        <!-- <pre>{{arregloCarro}}</pre> -->
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
                      <!-- //data.item.id -->
                        <template v-slot:cell(sku)="data">{{
                          data.item.sku

                        }}</template>
                        <template v-slot:cell(cantidad)="data">
                          <input
                          :disabled="desactivar"
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
                            :value="data.item.Cantidad"
                          />
                        </template>

                        <template v-slot:cell(unidad)="data" >
                            <input
                            :disabled="desactivar"
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
                             :value="data.item.UnidadMedida"
                          />
                        </template>

                        <template v-slot:cell(producto)="data">
                          <b>{{ data.item.NombreProducto }}</b>
                          <!-- <br />
                          <em
                            >No Informada (Sí agrega productos o quita no afecta en el stock)</em
                          > -->
                        </template>
                        <template v-slot:cell(afecto)="data">
                            <!-- {{data.item.Afecto}} -->
                            <select :disabled="desactivar" :value="(data.item.Afecto)?data.item.Afecto:'true'"
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
                            <select :disabled="desactivar"  :value="(data.item.TipoImpAdicional)?data.item.TipoImpAdicional:'0'"
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
                            <input :disabled="desactivar"
                            @click="ingresar_mia_carro(data.index, $event.target.value)"
                            @input="ingresar_mia_carro(data.index, $event.target.value)"
                             :value="data.item.MontoImpAdicional" class="form-control form-control-sm" type="text" name="input_monto_impuesto_adicional" >
                        </template>
                        <template v-slot:cell(precioProd)="data"
                          >$ {{ formatPrice(data.item.PrecioNeto) }}</template>

                         <template v-slot:cell(descuento)="data">
                            <input :disabled="desactivar" type="number"
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
                              (data.item.PrecioNeto * data.item.Cantidad) - Math.round((data.item.PrecioNeto * data.item.Cantidad) * ((data.item.descuento)?data.item.descuento:0) / 100)
                            )
                          }}</template
                        >
                        <template v-slot:cell(opc)="data">
                          <div class="col-12 col-xl-12">
                            <b-button
                            :disabled="desactivar"
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
            </b-container>
            </b-card>

             <!-- MODAL PRE NOTA DE CREDITO -->
         <b-modal ref="modal_factura" no-close-on-esc
                            no-close-on-backdrop hide-footer title="Facturación electronica (DTE 33)">

            <div class="modal-bodyxx">
                <!-- <pre>{{ pre_nc }}</pre> -->
                <!-- si es que el documento a previsualizar es una factura electronica 33 -->
                <div v-if="pre_nc.tipo_venta_id == '33'">
                     <div >
                        <!-- AQUI EL DISEÑO DE LA FACTURA ELECTRONICA -->
                        <div class="factura">
                            <center style="font-size: 2rem; border:3px solid red;color:red;font-family:sans-serif;">
                            <b class="upper"><pre style="color:red">R.U.T {{ pre_nc.documento.configuraciones.emisor.rut }}</pre></b>
                            <b><pre style="color:red">NOTA DE CREDITO <br>ELECTRONICA</pre></b>
                            <b><pre style="color:red">Nº XXXX</pre></b>
                            </center>
                            <br />
                            <center style="font-size: 2rem;font-family:sans-serif;">
                            <pre> S.I.I LOS ANGELES</pre>
                            </center>
                            <!-- <br /> -->
                            <center style="font-size: 2rem;font-family:sans-serif;">
                            <pre> {{ pre_nc.documento.configuraciones.emisor.empresa }}</pre>
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
                            <label class="upper"><b>DIRECCION: </b> {{pre_nc.documento.configuraciones.emisor.direccion}}</label><br>
                            <label class="upper"><b>GIRO: </b>{{ pre_nc.documento.configuraciones.emisor.giro }}</label> <br>


                           <b>EMISION&nbsp;: </b> {{ pre_nc.fecha_nota_credito | moment("DD/MM/YYYY") }} <br />
                           <b class="upper">MEDIO DE PAGO&nbsp;: </b> {{ pre_nc.documento.factura.FormaPago_str }} <br /><br>
                           <b class="upper">SEÑOR(A)&nbsp;: </b> {{ pre_nc.documento.factura.Cliente.RazonSocial }} <br />
                            <b class="upper">RUT&nbsp;: </b> {{ pre_nc.documento.factura.Cliente.Rut }} <br />
                            <b class="upper">DIRECCION(A)&nbsp;: </b> {{ pre_nc.documento.factura.Cliente.Direccion }} <br />
                            <b class="upper">COMUNA(A)&nbsp;: </b> {{ pre_nc.documento.factura.Cliente.Comuna }} <br />
                            <b class="upper">CIUDAD(A)&nbsp;: </b> {{ pre_nc.documento.factura.Cliente.Ciudad }} <br />

                            <b class="upper">GIRO&nbsp;: </b> {{ pre_nc.documento.factura.Cliente.Giro }} <br />

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

                                <tr v-for="c in pre_nc.documento.factura.Productos " :key="c.id">
                                    <td style="width:20px">{{ c.NombreProducto  }}</td>
                                    <td>{{ formatPrice(c.PrecioNeto) }}</td>
                                    <td>{{ c.Cantidad }}</td>
                                    <td>{{ c.UnidadMedida }}</td>
                                    <!-- <td>{{ formatPrice(c.PrecioNeto * c.Cantidad) }}</td> -->
                                    <!-- <td>{{ formatPrice(c.SubTotal) }}</td> -->
                                    <td>{{ formatPrice(c.item_descontado) }}</td>
                                </tr>


                                <tr>
                                <td
                                    class="fintabla"
                                    style="
                                    border-bottom: 1px solid black;
                                    border-top: 1px solid black;
                                    "
                                    colspan="1"
                                >
                                    <div style="text-align: right">REFERENCIA&nbsp;:</div>
                                </td>
                                <td
                                colspan="4"
                                    class="fintabla"
                                    style="
                                    border-bottom: 1px solid black;
                                    border-top: 1px solid black;
                                    "
                                >
                                <label for="">{{razon}}, folio Nº {{folio}} con fecha {{ref_fecha}} </label>
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
                                <!-- $ aqui es solo cuando se anula una factura -->
                                <label v-if="dte_precio=='neto'"> $ {{ formatPrice( pre_nc.documento.venta.venta_total )}} </label>
                                <label v-if="dte_precio=='iva_incluido'"> $ {{ formatPrice(pre_nc.documento.venta.venta_total)  }}</label>
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
                            :src="'https://barcode.tec-it.com/barcode.ashx?data='+'ted'+'&code=PDF417&multiplebarcodes=false&translate-esc=false&unit=Fit&dpi=96&imagetype=Gif&rotation=0&color=%23000000&bgcolor=%23ffffff&codepage=Default&qunit=Mm&quiet=0'"
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
                <!-- FIN si es que el documento a previsualizar es una factura electronica 33 -->
            </div>
         </b-modal>
        </div>



    </div>
</template>
<script src="../nota_credito/generar_nota_credito.js" >
</script>
