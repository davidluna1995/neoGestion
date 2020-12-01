<template>
  <div class="container-fluid mt-4">

        <b-card class="text-center tituloTabla transparencia mb-4 ">
        <b-card-header class="fondoProductoAdm mb-4">MIS VENTAS</b-card-header>

          <b-container class="fondoTotal col-12">
            <div class="row justify-content-center">
                <div class="col-md-10 ">
                   <div class="row ">
                       <div class="col-md-4">
                           <label for="">Desde:</label>
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
                                    <button @click="traer_caja_vendedor" class="btn btn-sm btn-success btn-block my-2">Filtrar</button>

                                    <button class="btn btn-info btn-block btn-sm my-2">Reload</button>


                       </div>
                   </div>


                </div>
            </div>
          </b-container>

        </b-card>

        <b-card class="text-center transparencia my-2" v-if="view_tabla">
                <center><h5>MIS CAJAS</h5></center>
                <!-- <pre>{{tabla}}</pre> -->
                <div class="table-responsive">
                    <table class="table">

                        <tr style="background:rgb(52, 58, 64); color:white">
                            <td><b>ID caja registro</b></td>
                            <td><b>Caja</b></td>
                            <td><b>Fecha apertura</b></td>
                            <td><b>fecha cierre</b></td>
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

                                <b-button class="btn-sm" @click="cargar_ventas(t);abrir_modal('modal-ventas')" variant="outline-light" v-b-tooltip.hover.rightbottom
                                title="ID caja registro: utilizado para ver las ventas asociadas a este ID">
                                    {{ t.registro_caja_vendedor_id }}
                                </b-button>
                            </td>
                            <td><i class="fas fa-cash-register"></i> {{ t.nombre }}</td>
                            <td>{{ t.mi_fecha_inicio }}</td>
                            <td>{{ t.mi_fecha_cierre }}</td>
                            <td>$ {{ formatPrice(t.mi_monto_inicio) }}</td>
                            <td>$ {{ formatPrice(t.mi_monto_cierre) }}</td>
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
                            <h6 class="text-center">ID caja registro {{registro_caja_vendedor }} - Ventas</h6>

                    </template>
                    <section>
                            <!-- TABLA DE LAS VENTAS -->
                            <!-- <pre>{{tabla_venta}}</pre> -->
                            <div class="table-responsive">
                                    <!-- <div class="d-flex justify-content-center">
                                        <button class="btn btn-outline-success btn-sm" @click="exportar_tabla"><i class="fas fa-file-csv fa-4x"></i></button>
                                    </div> -->
                                    <br>
                                <table id="tabla_data" class="table">
                                    <tr class="text-center" style="background:rgb(52, 58, 64); color:white">
                                        <td><b style="color:white" >Monto apertura</b></td>
                                        <td><b style="color:white" >Monto cierre</b></td>
                                        <td><b style="color:white" >Efectivo real</b></td>
                                        <td><b style="color:white" >Debito</b></td>
                                        <td><b style="color:white" >Vuelto</b></td>
                                        <td colspan="2"><b style="color:white"> Opción</b> </td>
                                    </tr>
                                 <tr class="text-center">

                                    <td style="background:white;">$ {{ formatPrice(v_monto_apertura) }}</td>

                                    <td style="background:white;">$ {{formatPrice(v_monto_cierre)}}</td>

                                    <td style="background:white;">$ {{ formatPrice(cabeza_venta.efectivo_real) }}</td>

                                    <td style="background:white;">$ {{ formatPrice(cabeza_venta.debito) }}</td>

                                    <td style="background:white;">$ {{ formatPrice(cabeza_venta.vuelto) }}</td>
                                    <td style="background:white;">  <button class="btn btn-outline-success btn-sm" @click="exportar_tabla('tabla_data')"><i class="fas fa-file-csv"></i>Exportar a excel</button></td>
                                </tr>
                                <tr class="text-center" style="background:rgb(52, 58, 64); color:white">

                                    <td colspan="7" style="background:white;">&nbsp;</td>


                                </tr>
                                <tr class="text-center" style="background:rgb(52, 58, 64); color:white">
                                    <td><b style="color:white">ID Venta</b></td>
                                    <td><b style="color:white">Cliente</b></td>
                                    <td><b style="color:white">Tipo entrega</b></td>
                                    <td><b style="color:white">Pago efectivo</b></td>
                                    <td><b style="color:white">Pago debito</b></td>
                                    <td><b style="color:white">Vuelto</b></td>
                                    <td><b style="color:white">Venta total</b></td>
                                    <!-- <td><b style="color:white">Monto actual</b></td> -->

                                    <!-- <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td> -->

                                </tr>
                                <tr class="text-center" v-for="(t) in tabla_venta" :key="t.id">
                                    <td style="background:rgb(52, 58, 64); color:white">

                                        <!-- <b-button class="btn-sm" @click="cargar_ventas(t);abrir_modal('modal-ventas')" variant="outline-dark" v-b-tooltip.hover
                                        title="ID caja registro: utilizado para ver las ventas asociadas a este ID"> -->
                                            <!-- {{ t.id }} -->
                                            {{ t.id }}
                                        <!-- </b-button> -->
                                    </td>
                                    <td>{{ t.cliente }}</td>
                                    <td>{{ t.entrega }}</td>
                                    <td>$ {{ formatPrice(t.pago_efectivo) }}</td>
                                    <td>$ {{ formatPrice(t.pago_debito) }}</td>
                                    <td>$ {{ formatPrice(t.vuelto) }}</td>
                                    <td>$ {{ formatPrice(t.venta_total) }}</td>
                                    <!-- <td>{{ t.saldo_actual_raw }}</td> -->
                                </tr>
                            </table>
                            </div>
                    </section>
        </b-modal>

  </div>
</template>

<script>

import XLSX from 'xlsx';

export default {
    components:{

    },
    data(){
        return{
            fecha_d:'',
            fecha_h:'',
            hora_d:'00:00',
            hora_h:'23:59',
            tabla:[],
            view_tabla:false,

            tabla_venta:[],
            cabeza_venta:[],
            registro_caja_vendedor:'',
            v_desde:'',
            v_hasta:'',
            v_monto_apertura:0,
            v_monto_cierre:0
        }
    },

    methods:{
         // MODAL VENTAS
        abrir_modal(ref){
            this.$refs[""+ref+""].show();
        },
        traer_caja_vendedor(){
            const data = {
                'fecha_d': this.fecha_d,
                'fecha_h': this.fecha_h,
                'hora_d': this.hora_d,
                'hora_h': this.hora_h
            }
            this.axios.post('api/mis_ventas', data).then((res)=>{
                if(res.data.estado == 'success'){
                    this.tabla = res.data.tabla;
                    this.view_tabla = true;
                }else{
                    this.view_tabla = false;
                }
            });
        },

        cargar_ventas($t){

            this.registro_caja_vendedor = $t.registro_caja_vendedor_id;
            this.v_desde =$t.mi_fecha_inicio;
            this.v_hasta =$t.mi_fecha_cierre;
            this.v_monto_apertura = $t.mi_monto_inicio;
            this.v_monto_cierre = $t.mi_monto_cierre;
            this.axios.get('api/mis_ventas_id/'+$t.registro_caja_vendedor_id+'/'+$t.mi_monto_inicio).then((res)=>{
                this.tabla_venta = res.data.tabla;
                this.cabeza_venta = res.data.cabeza;
            });
        },
        formatPrice(value) {
            let val = (value / 1).toFixed(0).replace('.', ',')
            return val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",")
        },

        exportar_tabla: function(){

            var wb = XLSX.utils.book_new();
            wb.SheetNames.push("Test sheet1");

            // var ws_data = [['hola','mundo','como tas']];

            var ws = XLSX.utils.table_to_sheet(document.getElementById('tabla_data'))
            wb.Sheets["Test sheet1"] = ws;


            // var ws2 = XLSX.utils.table_to_sheet(document.getElementById('printVenta'))
            // wb.SheetNames.push("Test sheet2");
            // wb.Sheets["Test sheet2"] = ws2;



            XLSX.writeFile(wb, `test.xlsx`,{ flag: 'w+' })

        },
    }
}
</script>

<style>
    .fondoProductoAdm{
        background: #fc4a1a;  /* fallback for old browsers */
        background: -webkit-linear-gradient(to right, #f7b733, #fc4a1a);  /* Chrome 10-25, Safari 5.1-6 */
        background: linear-gradient(to right, #f7b733, #fc4a1a); /* W3C, IE 10+/ Edge, Firefox 16+, Chrome 26+, Opera 12+, Safari 7+ */
        color: azure;
    }
</style>
