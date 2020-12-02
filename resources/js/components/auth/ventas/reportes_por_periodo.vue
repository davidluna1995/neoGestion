<template>
  <div v-if="usuario.rol==admin">
    <div class="container-fluid mt-4">

        <b-card class="text-center tituloTabla transparencia mb-4">
        <b-card-header class="fondoProductoAdm">REPORTES POR PERIODO</b-card-header>

          <b-container class="fondoTotal  col-12">
            <div class="row justify-content-center">
                <div class="col-md-10">
                    <div class="row my-2">
                        <!-- <div class="col-md-8">
                            <label for="">Caja:</label>
                            <select v-model="caja" class="form-control form-control-sm my-2 " name="" id="">
                                <option value="0">--TODAS LAS CAJAS--</option>
                                <option v-for="c in select_caja" :key="c.id" :value="c.id">{{c.nombre+' - '+c.descripcion}}</option>
                            </select>
                        </div> -->
                    </div>
                   <div class="row">
                       <div class="col-md-4">
                           <label >Desde:</label>
                           <input v-model="fecha_d" class="form-control my-2 form-control-sm" type="date" name="" id="">
                           <input v-model="hora_d" class="form-control my-2 form-control-sm" type="time" name="" id="">
                       </div>
                       <div class="col-md-4">
                           <label for="">Hasta:</label>
                            <input v-model="fecha_h" class="form-control form-control-sm my-2" type="date" name="" id="">
                            <input v-model="hora_h" class="form-control form-control-sm my-2" type="time" name="" id="">
                       </div>

                       <div class="col-md-4 ">
                                    <label for="">Opciones:</label>
                                    <button @click="traer_caja_reporte" class="btn btn-sm btn-success btn-block my-2">Filtrar</button>

                                    <button v-if="view_tabla" @click="exportar_tabla('tabla_caja')" class="btn btn-outline-success btn-block btn-sm my-2"><i class="fas fa-file-csv"></i> Exportar a excel</button>

                       </div>

                   </div>


                </div>
            </div>
          </b-container>

        </b-card>

        <b-card class="text-center transparencia my-2" v-if="view_tabla">
                <center><h5>PERIODOS</h5></center>
                <!-- <pre>{{tabla}}</pre> -->
                <div class="table-responsive">
                    <table id="tabla_caja" class="table">

                        <tr style="background:rgb(52, 58, 64); color:white">
                            <td><b>ID Periodo</b></td>
                            <td><b>Estado</b></td>
                            <td><b>Fecha apertura</b></td>
                            <td><b>Fecha cierre</b></td>
                            <!-- <td><b>fecha cierre</b></td> -->
                            <td><b>Monto apertura</b></td>
                            <td><b>Monto cierre</b></td>
                            <!-- <td><b>Monto final de caja</b></td> -->
                            <!-- <td></td>
                            <td></td>
                            <td></td>
                            <td></td> -->

                        </tr>
                        <tr v-for="t in tabla" :key="t.id">
                            <td style="background:rgb(52, 58, 64); color:white">
                                <!-- <button type="button" class="btn btn-outline-dark">
                                {{ t.registro_caja_vendedor_id }}</button> -->

                                <b-button class="btn-sm" @click="cargar_cajas(t);abrir_modal('modal-ventas')" variant="outline-light" v-b-tooltip.hover.rightbottom
                                title="ID Periodo: utilizado para ver las cajas asociadas al periodo">
                                    {{ t.periodo_id }}
                                </b-button>
                            </td>
                            <td><i class="fas fa-cash-register"></i> {{ t.estado }}</td>
                            <td>{{ t.fecha_inicio }}</td>
                            <td>{{ t.fecha_cierre }}</td>
                            <!-- <td>{{ t.mi_fecha_cierre }}</td> -->
                            <td>$ {{ formatPrice(t.monto_inicio) }}</td>
                            <td>$ {{ formatPrice(t.monto_cierre) }}</td>
                            <!-- <td><label style="color:#52BE80" for="">$ {{ formatPrice(t.mi_monto_cierre + t.mi_monto_inicio) }}</label></td> -->
                        </tr>
                    </table>
                </div>

        </b-card>



        <b-modal
                    no-close-on-esc
                    no-close-on-backdrop
                    class="modal-header-ventas"
                    id="modal-ventas"
                    size="xl"
                    :ref="'modal-ventas'"
                    hide-footer
                    centered
                >
                    <template v-slot:modal-title>
                            <h6 class="text-center">ID Periodo {{periodo_id }} - Ventas</h6>

                    </template>
                    <section>
                            <!-- TABLA DE LAS VENTAS -->
                            <!-- <pre>{{tabla_venta}}</pre> -->
                            <div class="table-responsive">
                                    <!-- <div class="d-flex justify-content-center"> -->
                                        <!-- <button class="btn btn-outline-success btn-sm" @click="exportar_tabla('tabla_data')"><i class="fas fa-file-csv fa-4x"></i></button> -->
                                    <!-- </div> -->
                                    <br>
                                <table id="tabla_data" class="table">
                                    <tr class="text-center" style="background:rgb(52, 58, 64); color:white">
                                        <td><b style="color:white" >Monto apertura</b></td>
                                        <td><b style="color:white" >Monto cierre</b></td>
                                        <!-- <td><b style="color:white" >Efectivo real</b></td>
                                        <td><b style="color:white" >Debito</b></td>
                                        <td><b style="color:white" >Vuelto</b></td> -->
                                        <td colspan="2"><b style="color:white"> Opción</b> </td>
                                    </tr>
                                 <tr class="text-center">

                                    <td style="background:white;">$ {{ formatPrice(v_monto_apertura) }}</td>

                                    <td style="background:white;">$ {{formatPrice(v_monto_cierre)}}</td>
<!--
                                    <td style="background:white;">$ {{ formatPrice(cabeza_venta.efectivo_real) }}</td>

                                    <td style="background:white;">$ {{ formatPrice(cabeza_venta.debito) }}</td>

                                    <td style="background:white;">$ {{ formatPrice(cabeza_venta.vuelto) }}</td> -->
                                    <td style="background:white;">  <button class="btn btn-outline-success btn-sm" @click="exportar_tabla('tabla_data')"><i class="fas fa-file-csv"></i>Exportar a excel</button></td>
                                </tr>
                                <tr class="text-center" style="background:rgb(52, 58, 64); color:white">

                                    <td colspan="7" style="background:white;">&nbsp;</td>


                                </tr>
                                <tr class="text-center" style="background:rgb(52, 58, 64); color:white">
                                    <td><b style="color:white">ID Registro caja</b></td>
                                    <td><b style="color:white">Caja</b></td>
                                    <td><b style="color:white">Vendedor</b></td>
                                    <td><b style="color:white">Fecha apertura</b></td>
                                    <td><b style="color:white">Fecha cierre</b></td>
                                    <td><b style="color:white">Monto apertura</b></td>
                                    <td><b style="color:white">Monto cierre</b></td>
                                    <!-- <td><b style="color:white">Monto actual</b></td> -->

                                    <!-- <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td> -->

                                </tr>
                                <tr class="text-center" v-for="(t) in tabla_caja" :key="t.id">
                                    <td style="background:rgb(52, 58, 64); color:white">

                                        <!-- <b-button class="btn-sm" @click="cargar_ventas(t);abrir_modal('modal-ventas')" variant="outline-dark" v-b-tooltip.hover
                                        title="ID caja registro: utilizado para ver las ventas asociadas a este ID"> -->
                                            <!-- {{ t.id }} -->
                                            {{ t.registro_caja_id }}
                                        <!-- </b-button> -->
                                    </td>
                                    <td>{{ t.nombre }}</td>
                                    <td>{{ t.vendedor }}</td>
                                    <td>$ {{ t.fecha_inicio }}</td>
                                    <td>$ {{ t.fecha_cierre }}</td>
                                    <td>$ {{ formatPrice(t.monto_inicio) }}</td>
                                    <td>$ {{ formatPrice(t.monto_cierre) }}</td>
                                    <!-- <td>{{ t.saldo_actual_raw }}</td> -->
                                </tr>
                            </table>
                            </div>
                    </section>
        </b-modal>

  </div>

    <!-- <button @click="download">Download</button> -->
  </div>
</template>

<script>


import XLSX from 'xlsx';

    export default {
            components:{

            },
            data(){
                return{
                    usuario: this.$auth.user(),
                    admin:1,

                    select_caja:'',
                    fecha_d:'',
                    fecha_h:'',
                    hora_d:'00:00',
                    hora_h:'23:59',
                    tabla:[],
                    view_tabla:false,

                    tabla_caja:[],
                    cabeza_venta:[],
                    periodo_id:'',
                    v_desde:'',
                    v_hasta:'',
                    v_monto_apertura:0,
                    v_monto_cierre:0,

                    caja:'0'
                }
            },

            methods:{
                 // MODAL VENTAS
                abrir_modal(ref){
                    this.$refs[""+ref+""].show();
                },
                traer_cajas(){
                    this.axios.get('api/all_cajas').then((res)=>{
                        this.select_caja = res.data.caja;
                    });
                },
                traer_caja_reporte(){
                    const data = {
                        'fecha_d': this.fecha_d,
                        'fecha_h': this.fecha_h,
                        'hora_d': this.hora_d,
                        'hora_h': this.hora_h,
                        'caja': this.caja
                    }
                    this.axios.post('api/reporte_periodo', data).then((res)=>{
                        if(res.data.estado == 'success'){
                            this.tabla = res.data.tabla;
                            this.view_tabla = true;
                        }else{
                            this.view_tabla = false;
                        }
                    });
                },

                cargar_cajas($t){

                    this.periodo_id = $t.periodo_id;
                    this.v_desde = $t.fecha_inicio;
                    this.v_hasta = $t.fecha_cierre;
                    this.v_monto_apertura = $t.monto_inicio;
                    this.v_monto_cierre = $t.monto_cierre;
                    this.axios.get('api/cajas_periodo/'+$t.periodo_id).then((res)=>{
                        if(res.data.estado == 'success'){
                            this.tabla_caja = res.data.tabla;
                        }

                        // this.cabeza_venta = res.data.cabeza;
                    });
                },
                formatPrice(value) {
                    let val = (value / 1).toFixed(0).replace('.', ',')
                    return val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",")
                },

                exportar_tabla: function($id_tabla){

                    var wb = XLSX.utils.book_new();
                    wb.SheetNames.push("Test sheet1");

                    // var ws_data = [['hola','mundo','como tas']];

                    var ws = XLSX.utils.table_to_sheet(document.getElementById(''+$id_tabla+''))
                    wb.Sheets["Test sheet1"] = ws;


                    // var ws2 = XLSX.utils.table_to_sheet(document.getElementById('printVenta'))
                    // wb.SheetNames.push("Test sheet2");
                    // wb.Sheets["Test sheet2"] = ws2;



                    XLSX.writeFile(wb, `test.xlsx`,{ flag: 'w+' })

                },
            },

            created(){
                this.traer_cajas();
            }
}

</script>

<style scoped src="../ventas/reportes_por_caja.css"></style>
