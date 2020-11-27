<template>
  <div class="container-fluid">

        <b-card class="text-center transparencia">

            <div class="row justify-content-center">
                <div class="col-md-10">
                   <div class="row">
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


        </b-card>

        <b-card class="text-center transparencia my-2" v-if="view_tabla">

                <!-- <pre>{{tabla}}</pre> -->
                <div class="table-responsive">
                    <table class="table">

                        <tr class="table-dark">
                            <td><b>ID caja registro</b></td>
                            <td><b>Caja</b></td>
                            <td><b>Fecha apertura</b></td>
                            <td><b>fecha cierre</b></td>
                            <td><b>Monto apertura</b></td>
                            <td><b>Monto cierre</b></td>
                            <td><b>Monto final de caja</b></td>
                            <!-- <td></td>
                            <td></td>
                            <td></td>
                            <td></td> -->

                        </tr>
                        <tr v-for="t in tabla" :key="t.id">
                            <td>
                                <!-- <button type="button" class="btn btn-outline-dark">
                                {{ t.registro_caja_vendedor_id }}</button> -->

                                <b-button class="btn-sm" @click="cargar_ventas(t);abrir_modal('modal-ventas')" variant="outline-dark" v-b-tooltip.hover.rightbottom
                                title="ID caja registro: utilizado para ver las ventas asociadas a este ID">
                                    {{ t.registro_caja_vendedor_id }}
                                </b-button>
                            </td>
                            <td><i class="fas fa-cash-register"></i> {{ t.nombre }}</td>
                            <td>{{ t.mi_fecha_inicio }}</td>
                            <td>{{ t.mi_fecha_cierre }}</td>
                            <td>$ {{ formatPrice(t.mi_monto_inicio) }}</td>
                            <td>$ {{ formatPrice(t.mi_monto_cierre) }}</td>
                            <td><label style="color:#52BE80" for="">$ {{ formatPrice(t.mi_monto_cierre + t.mi_monto_inicio) }}</label></td>
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
                                <table class="table">
                                 <tr style="background:#34495E;">
                                    <th colspan="6"><p style="color:white" class="text-right">Monto apertura</p></th>
                                    <td style="background:white;">{{ v_monto_apertura }}</td>
                                </tr>
                                <tr style="background:#34495E;">
                                    <td><b style="color:white">ID Venta</b></td>
                                    <td><b style="color:white">Cliente</b></td>
                                    <td><b style="color:white">Tipo entrega</b></td>
                                    <td><b style="color:white">Pago efectivo</b></td>
                                    <td><b style="color:white">Pago debito</b></td>
                                    <td><b style="color:white">Vuelto</b></td>
                                    <td><b style="color:white">Venta total</b></td>
                                    <td><b style="color:white">Monto actual</b></td>

                                    <!-- <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td> -->

                                </tr>
                                <tr v-for="(t) in tabla_venta" :key="t.id">
                                    <td>

                                        <b-button class="btn-sm" @click="cargar_ventas(t);abrir_modal('modal-ventas')" variant="outline-dark" v-b-tooltip.hover
                                        title="ID caja registro: utilizado para ver las ventas asociadas a este ID">
                                            <!-- {{ t.id }} -->
                                            {{ t.id }}
                                        </b-button>
                                    </td>
                                    <td>{{ t.cliente }}</td>
                                    <td>{{ t.entrega }}</td>
                                    <td>{{ t.pago_efectivo }}</td>
                                    <td>{{ t.pago_debito }}</td>
                                    <td>{{ t.vuelto }}</td>
                                    <td>{{ t.venta_total }}</td>
                                    <td>{{ t.saldo_actual_raw }}</td>
                                </tr>
                            </table>
                            </div>
                    </section>
                </b-modal>

  </div>
</template>

<script>

export default {
    components:{

    },
    data(){
        return{
            fecha_d:'',
            fecha_h:'',
            hora_d:'',
            hora_h:'',
            tabla:[],
            view_tabla:false,

            tabla_venta:[],
            registro_caja_vendedor:'',
            v_desde:'',
            v_hasta:'',
            v_monto_apertura:''
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
                this.tabla = res.data;
                this.view_tabla = true;
            });
        },

        cargar_ventas($t){

            this.registro_caja_vendedor = $t.registro_caja_vendedor_id;
            this.v_desde =$t.mi_fecha_inicio;
            this.v_hasta =$t.mi_fecha_cierre;
            this.v_monto_apertura = $t.mi_monto_inicio;
            this.axios.get('api/mis_ventas_id/'+$t.registro_caja_vendedor_id+'/'+$t.mi_monto_inicio).then((res)=>{
                this.tabla_venta = res.data;
            });
        },
        formatPrice(value) {
            let val = (value / 1).toFixed(0).replace('.', ',')
            return val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".")
        },
    }
}
</script>

<style>

</style>
