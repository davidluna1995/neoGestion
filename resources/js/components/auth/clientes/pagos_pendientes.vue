<template>
  <div>
    <div class="row m-4">
      <div class="col-12 col-md-12 col-lg-12">
        <!-- FORMULARIO -->
        <b-card class="text-center tituloTabla transparencia">
          <b-card-header class="fondoCategoria mb-4">Pagos por pagar</b-card-header>


          <b-card-body>
             <div class="table-responsive">
                  <b-table
                    show-empty
                    emptyText="Buscando clientes..."
                    small
                    striped
                    hover
                    bordered
                    stacked="lg"
                    head-variant="dark"
                    :fields="cabeza"
                    :items="get_tabla"
                    >
                    <template v-slot:cell(venta_id)="data">
                        <div class="col-12">{{ data.item.venta_id }}</div>
                    </template>
                    <template v-slot:cell(cliente)="data">
                        <div class="col-12">{{ data.item.cliente }}</div>
                    </template>
                    <template v-slot:cell(contacto)="data">
                        <div class="col-12">{{ data.item.contacto }}</div>
                    </template>
                    <template v-slot:cell(detalle_credito)="data">
                        <div class="col-12">{{ data.item.detalle_credito }}</div>
                    </template>
                    <template v-slot:cell(monto_credito)="data">
                        <div class="col-12">$ {{ formatPrice(data.item.monto_credito) }}</div>
                    </template>
                    <template v-slot:cell(fecha)="data">
                        <div class="col-12">{{ data.item.fecha }}</div>
                    </template>

                    <template v-slot:cell(pago)="data">
                        <div class="col-12">{{ data.item.pago }}</div>
                    </template>
                    <template v-slot:cell(monto_pago)="data">
                        <div class="col-12">{{ formatPrice(data.item.monto_pago) }}</div>
                    </template>
                    <template v-slot:cell(descripcion)="data">
                        <div class="col-12">{{ data.item.descripcion }}</div>
                    </template>
                    <template v-slot:cell(fecha_pago)="data">
                        <div class="col-12">{{ data.item.fecha_pago }}</div>
                    </template>
                    <!-- <template v-slot:cell(activo)="data">
                        <div class="col-12">{{ data.item.activo }}</div>
                    </template> -->
                    <template v-slot:cell(opciones)="data">
                        <div class="col-12">
                            <b-button

                             v-b-modal="'modal'+data.item.venta_id">Pagar</b-button>

                            <b-modal hide-footer="" :id="'modal'+data.item.venta_id" :title="'Opciones para '+data.item.cliente">
                                <fieldset class="scheduler-border">
                                    <legend class="scheduler-border">Pagar</legend>
                                    <div class="control-group">
                                        <label class="control-label input-label">Monto a pagar:</label>
                                        <input id="input_monto_credito" v-model="data.item.monto_credito" class="form-control" type="number">
                                        <br>
                                        <label for="">Observacion del pago (Opcional)</label>
                                        <textarea class="form-control" placeholder="Detalle el pago (Opcional)..." name="" id="text_detalle" cols="30" rows="10"></textarea>
                                        <!-- <b-button :disabled="btn_actualizar"
                                                @click="actualizar"
                                                variant="success">Actualizar
                                                <b-spinner v-if="btn_actualizar" small label="Small Spinner"></b-spinner></b-button>
                                             <b-button :disabled="btn_inhabilitar"  @click="inhabilitar(data.item.venta_id)" variant="danger">Inhabilitar
                                            <b-spinner v-if="btn_inhabilitar" small label="Small Spinner"></b-spinner>
                                        </b-button> -->
                                        <br>
                                        <button @click="pagar(data.item.venta_id)"  class="btn btn-success btn-block" :disabled="btn_actualizar">
                                            Pagar
                                        </button>

                                        <!-- <b-alert
                                        :show="a_dismissCountDown"
                                            dismissible
                                            variant="success"

                                        >
                                        <b>{{a_correcto}}</b>
                                        </b-alert>

                                        <b-alert
                                            :show="a_dismissCountDown3"
                                            variant="warning"
                                            @dismissed="a_dismissCountDown3=0"
                                            @dismiss-count-down="a_countDownChanged3"
                                            >
                                            <b>{{a_errores}}</b>
                                            </b-alert> -->


                                    </div>
                                </fieldset>
                            </b-modal>
                        </div>
                    </template>

            </b-table>
             </div>
          </b-card-body>
        </b-card>
      </div>
    </div>
  </div>
</template>

<script>
export default {
    data(){
        return{
            cabeza: [
                { key: 'venta_id', label: 'ID venta', variant: 'dark' },
                { key: 'cliente', label: 'Cliente' },
                { key: 'contacto', label: 'Contacto' },
                { key: 'detalle_credito', label: 'Detalle del credito' },
                { key: 'monto_credito', label: 'Monto del credito' },
                { key: 'fecha', label: 'Fecha venta' },
                { key: 'pago', label:'Pago' },
                { key: 'monto_pago', label:'Monto' },
                { key: 'descripcion', label:'Detalle' },
                { key: 'fecha_pago', label:'Fecha pago'},
                { key: 'opciones', label: 'opciones' },
            ],
            get_tabla:[],
            btn_actualizar:false
        }
    },
    created(){
        this.cliente_deuda();
    },

    methods:{
        cliente_deuda(){
            this.axios.get('api/cliente_deuda').then((res)=>{
                if(res.data.estado=='success'){
                    this.get_tabla = res.data.listar;
                }
            });
        },
        formatPrice(value) {
            let val = (value / 1).toFixed(0).replace('.', ',')
            return val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".")
        },

        pagar(venta_id){
            this.btn_actualizar = true;
            const monto_credito = document.getElementById('input_monto_credito').value;
            const det_credito = document.getElementById('text_detalle').value;

            const data = {
                venta_id : venta_id,
                monto_credito: monto_credito,
                detalle_credito: det_credito
            };

            this.axios.post('api/pagar_credito', data).then((res)=>{
                if(res.data.estado == 'success'){
                    this.btn_actualizar = false;
                    this.cliente_deuda();
                    alert(''+res.data.mensaje+'');
                }else{
                    this.btn_actualizar = false;
                    alert(''+res.data.mensaje+'');
                }
            });

        }
    },

}
</script>

<style>

</style>
