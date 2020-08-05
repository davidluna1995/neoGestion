<template>
  <div>
    <div class="row m-4">
      <div class="col-12 col-md-12 col-lg-12">
        <!-- FORMULARIO -->
        <b-card class="text-center tituloTabla transparencia">
          <b-card-header class="fondoCategoria mb-4">Clientes</b-card-header>


            <b-button v-b-modal.modal-1><i class="fas fa-user-plus"></i> Crear nuevo cliente</b-button>

            <b-modal hide-footer id="modal-1" title="Crear nuevo cliente">
                <label for="">Rut (Sin punto ni guión):</label>
                <b-form-input
                    id="input-1"
                    v-model="rut"
                    type="text"
                    required
                    placeholder="Ingrese rut.."
                    ></b-form-input>
                    <br>
                    <label for="">Nombres:</label>
                    <b-form-input
                        id="input-1"
                        v-model="nombres"
                        type="text"
                        required
                        placeholder="Ingrese nombres.."
                        ></b-form-input>
                    <br>
                    <label for="">Apellidos:</label>
                    <b-form-input
                        id="input-1"
                        v-model="apellidos"
                        type="text"
                        required
                        placeholder="Ingrese apellidos.."
                        ></b-form-input>
                    
                    <br>
                    <label for="">Contacto telefónico (Opcional):</label>
                    <b-form-input
                        id="input-1"
                        v-model="contacto"
                        type="text"
                        required
                        placeholder="Ingrese contacto telefónico.."
                        ></b-form-input>
                    
                    <br>
                    <label for="">Email (Opcional):</label>
                    <b-form-input
                        id="input-1"
                        v-model="email"
                        type="text"
                        required
                        placeholder="Ingrese email.."
                        ></b-form-input>
                    
                    <br>
                    <label for="">Dirección (Opcional):</label>
                    <b-form-input
                        id="input-1"
                        v-model="direccion"
                        type="text"
                        required
                        placeholder="Ingrese dirección.."
                        ></b-form-input>
                    <br>
                    <b-button :disabled="boton_save" @click="guardar" class="btn-block" variant="success"><i class="far fa-save"></i>
                     Guardar
                        <b-spinner v-if="boton_save" variant="primary" label="Spinning"></b-spinner> 
                    </b-button>
                    <b-alert
                    :show="dismissCountDown"
                        dismissible
                        variant="success"
                        @dismissed="dismissCountDown=0"
                        @dismiss-count-down="countDownChanged"
                    >
                    <b>{{correcto}} {{ dismissCountDown }} segundos...</b>
                    </b-alert>

                    <b-alert
                        :show="dismissCountDown3"
                        variant="warning"
                        @dismissed="dismissCountDown3=0"
                        @dismiss-count-down="countDownChanged3"
                        >
                        <b>{{errores3}} {{ dismissCountDown3 }} segundos...</b>
                        </b-alert>
            </b-modal>

            <hr>

            <div>
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
              :items="cuerpo"
            >
                <template v-slot:cell(rut)="data">
                    <div class="col-12">{{ data.item.rut }}</div>
                </template>
                <template v-slot:cell(apellidos)="data">
                    <div class="col-12">{{ data.item.apellidos }}</div>
                </template>
                <template v-slot:cell(nombres)="data">
                    <div class="col-12">{{ data.item.nombres }}</div>
                </template>
                <template v-slot:cell(contacto)="data">
                    <div class="col-12">{{ data.item.contacto }}</div>
                </template>
                <template v-slot:cell(email)="data">
                    <div class="col-12">{{ data.item.email }}</div>
                </template>
                <template v-slot:cell(direccion)="data">
                    <div class="col-12">{{ data.item.direccion }}</div>
                </template>
                <template v-slot:cell(opciones)="data">
                    <div class="col-12">
                        <b-button @click="
                                    a_cliente_id=data.item.id;
                                    a_nombres=data.item.nombres;
                                    a_apellidos=data.item.apellidos;
                                    a_contacto=data.item.contacto;
                                    a_email=data.item.email;
                                    a_direccion=data.item.direccion;
                                    btn_actualizar=false;
                        " v-b-modal="'modal'+data.item.id">Opciones</b-button>

                        <b-modal hide-footer="" :id="'modal'+data.item.id" :title="'Opciones para '+data.item.nombres+' '+data.item.apellidos">
                            <fieldset class="scheduler-border">
                                <legend class="scheduler-border">Actualizar información</legend>
                                <div class="control-group">
                                    <label class="control-label input-label">Nombres:</label>
                                    <b-input v-model="a_nombres" placeholder="Nombre.."></b-input>
                                    <br>
                                    <label class="control-label input-label">Apellidos:</label>
                                    <b-input v-model="a_apellidos" placeholder="Apellidos.."></b-input>
                                    <br>
                                    <label class="control-label input-label">Contacto:</label>
                                    <b-input v-model="a_contacto" placeholder="Contacto.."></b-input>
                                    <br>
                                    <label class="control-label input-label">Email:</label>
                                    <b-input v-model="a_email" placeholder="Email.."></b-input>
                                    <br>
                                    <label class="control-label input-label">Dirección:</label>
                                    <b-input v-model="a_direccion" placeholder="Dirección.."></b-input>
                                    <br>
                                    <b-button :disabled="btn_actualizar" 
                                              @click="actualizar" 
                                              variant="success">Actualizar 
                                              <b-spinner v-if="btn_actualizar" small label="Small Spinner"></b-spinner></b-button>
                                    <b-button :disabled="btn_inhabilitar"  @click="inhabilitar(data.item.id)" variant="danger">Inhabilitar
                                        <b-spinner v-if="btn_inhabilitar" small label="Small Spinner"></b-spinner>
                                    </b-button>

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

        </b-card>
      </div>
    </div>
  </div>
</template>









<script>
export default {
    data(){
        return{
            boton_save:false,
            btn_inhabilitar:false,
            rut:'',
            nombres:'',
            apellidos:'',
            contacto:'',
            email:'',
            direccion:'',
            //success
            dismissSecs: 5,
            dismissCountDown: 0,
            correcto: '',
            //error
            dismissCountDown3:0,
            errores3:'',
            dismissSecs3: 5,

            //success actualizar
            a_dismissSecs: 5,
            a_dismissCountDown: 0,
            a_correcto: '',
            //error actualizar
            a_dismissCountDown3:0,
            a_errores:'',
            a_dismissSecs3: 5,



            cabeza: [
                { key: 'rut', label: 'Rut', variant: 'dark' },
                { key: 'apellidos', label: 'Apellidos' },
                { key: 'nombres', label: 'Nombres' },
                { key: 'contacto', label: 'Contacto' },
                { key: 'email', label: 'Email' },
                { key: 'Direccion', label: 'Dirección' },
                { key: 'opciones', label: 'opciones' },
            ],
            cuerpo:[],

            //actualizar
            btn_actualizar:false,
            a_cliente_id:'',
            a_nombres:'',
            a_apellidos:'',
            a_contacto:'',
            a_email:'',
            a_direccion:''

        }
    },
    created(){
        this.listar();
    },
    methods:{

        guardar(){
            this.boton_save=true;
            if(this.validar_campos() == false){
                alert('el rut, nombre y apellidos son obligatorios');
                this.boton_save=false;
                return false;
            }else{
                const data = new FormData();
                data.append('rut', this.rut);
                data.append('nombres', this.nombres);
                data.append('apellidos', this.apellidos);
                data.append('contacto', this.contacto);
                data.append('email', this.email);
                data.append('direccion', this.direccion);

                this.axios.post('api/guardar_cliente', data).then((res)=>{
                    if(res.data.estado=='success'){
                        this.boton_save=false;
                        this.correcto = res.data.mensaje;
                        
                        this.showAlert();
                        this.limpiar();
                        this.listar();
                        
                    }else{
                        this.boton_save=false;
                        this.errores3 = res.data.mensaje;
                        this.showAlert3();
                    }
                });
            }
            
        },
        actualizar(){
            this.btn_actualizar = true;
            if(this.validar_campos_act() == false){
                alert('el nombre y apellidos son obligatorios');
                this.btn_actualizar=false;
                return false;
            }else{
                const data = new FormData();
                // data.append('rut', this.rut);
                data.append('cliente_id', this.a_cliente_id);
                data.append('nombres', this.a_nombres);
                data.append('apellidos', this.a_apellidos);
                data.append('contacto', this.a_contacto);
                data.append('email', this.a_email);
                data.append('direccion', this.a_direccion);

                this.axios.post('api/actualizar_cliente', data).then((res)=>{
                     if(res.data.estado=='success'){
                        this.btn_actualizar=false;
                        alert(res.data.mensaje)
                        // this.a_correcto = res.data.mensaje;
                        // this.a_showAlert();
                        this.listar();
                        
                    }else{
                        this.btn_actualizar=false;
                        alert(res.data.mensaje)
                        // this.a_errores = res.data.mensaje;
                        // this.a_showAlert3();
                    }
                });
            }

        },
        listar(){
            this.axios.get('api/listar_clientes').then((res)=>{
                if (res.data.estado=='success') {
                    this.cuerpo = res.data.cuerpo;
                }else{
                    this.cuerpo = {}
                }
            });
        },

        inhabilitar(){
            this.btn_inhabilitar=true;
            var r = confirm("¿Quiere inhabilitar el cliente con id "+this.a_cliente_id+'?');
            if (r == true) {
                this.axios.get('api/inhabilitar_cliente/' + this.a_cliente_id).then((res) => {
                    if (res.data.estado == 'success') {
                        this.listar();
                        this.btn_inhabilitar=false;
                        alert(res.data.mensaje)
                    } else {
                        this.btn_inhabilitar=false;
                        alert(res.data.mensaje)
                        console.log(res.data);
                    }
                });
            } else {
                this.btn_inhabilitar=false;
                alert("Proceso cancelado!")
            }
        },

        showAlert() {
            this.dismissCountDown = this.dismissSecs
        },
        showAlert3() {
            this.dismissCountDown3 = this.dismissSecs3
        },
        countDownChanged(dismissCountDown) {
            this.dismissCountDown = dismissCountDown
        },
        countDownChanged3(dismissCountDown3) {
            this.dismissCountDown3 = dismissCountDown3
        },

        a_countDownChanged(dismissCountDown) {
            this.a_dismissCountDown = a_dismissCountDown
        },
        a_countDownChanged3(dismissCountDown3) {
            this.a_dismissCountDown3 = a_dismissCountDown3
        },

        a_showAlert() {
            this.a_dismissCountDown = this.a_dismissSecs
        },
        a_showAlert3() {
            this.a_dismissCountDown3 = this.a_dismissSecs3
        },

        validar_campos(){
            var t = this;
            if(t.rut.trim()=='' || t.nombres.trim()==''|| t.apellidos.trim()==''){
                return false;
            }else{
                return true;
            }
        },
        
        limpiar(){
            this.rut='';
            this.nombres='';
            this.apellidos='';
            this.contacto='';
            this.email='';
            this.direccion='';
        },
        validar_campos_act(){
            var t = this;
            if(t.a_nombres.trim()==''|| t.a_apellidos.trim()==''){
                return false;
            }else{
                return true;
            }
        },
    }
}
</script>



<style >
fieldset.scheduler-border {
    border: 1px groove #ddd !important;
    padding: 0 1.4em 1.4em 1.4em !important;
    margin: 0 0 1.5em 0 !important;
    -webkit-box-shadow:  0px 0px 0px 0px #000;
            box-shadow:  0px 0px 0px 0px #000;
}

legend.scheduler-border {
    font-size: 1.2em !important;
    font-weight: bold !important;
    text-align: left !important;
}
</style>