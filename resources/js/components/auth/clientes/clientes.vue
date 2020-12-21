<template>
  <div>
    <div class="row m-4">
      <div class="col-12 col-md-12 col-lg-12">
        <!-- FORMULARIO -->
        <b-card class="text-center tituloTabla transparencia">
          <b-card-header class="fondoCategoria mb-4">Clientes</b-card-header>


            <b-button v-b-modal.modal-1><i class="fas fa-user-plus"></i> Crear nuevo cliente</b-button>

            <b-modal hide-footer id="modal-1" title="Crear nuevo cliente">

                <label for="">Tipo de cliente (*):</label>
                <select v-model="tipo_cliente" class="form-control" name="" id="">
                    <option value="">--SELECCIONE--</option>
                    <option value="PERSONA">Persona</option>
                    <option value="EMPRESA">Empresa</option>
                </select>
                <br>
                <label for="">Rut (*) (Sin punto ni guión) :</label>
                <input class="form-control" id="rut" @blur="formatear_rut" placeholder="Buscar rut.." type="text">

                   <div v-if="tipo_cliente == 'PERSONA'">
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
                   </div>

                   <div v-if="tipo_cliente == 'EMPRESA'">
                        <br>
                        <label for="">Razón social (*)</label>
                        <input v-model="razon_social" type="text" class="form-control" placeholder="Razón social">
                   </div>

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
                    <label for="">Dirección (*):</label>
                    <b-form-input
                        id="input-1"
                        v-model="direccion"
                        type="text"
                        required
                        placeholder="Ingrese dirección.."
                        ></b-form-input>
                    <br>
                     <label for="">Comuna (*):</label>
                    <b-form-input
                        id="input-1"
                        v-model="comuna"
                        type="text"
                        required
                        placeholder="Ingrese comuna.."
                        ></b-form-input>
                    <br>
                    <label for="">Ciudad (*):</label>
                    <b-form-input
                        id="input-1"
                        v-model="ciudad"
                        type="text"
                        required
                        placeholder="Ingrese ciudad.."
                        ></b-form-input>
                    <br>

                    <label for="">Giro (*):</label>
                    <b-form-input
                        id="input-1"
                        v-model="giro"
                        type="text"
                        required
                        placeholder="Ingrese giro.."
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
              :items="cuerpo"
            >
                <template v-slot:cell(rut)="data">
                    <div class="col-12">{{ data.item.rut }}</div>
                </template>
                <template v-slot:cell(cliente)="data">
                    <div class="col-12">{{ data.item.cliente }}</div>
                </template>
                 <template v-slot:cell(tipo)="data">
                    <div class="col-12">{{ data.item.tipo_cliente }}</div>
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
                <template v-slot:cell(comuna)="data">
                    <div class="col-12">{{ data.item.comuna }}</div>
                </template>
                <template v-slot:cell(ciudad)="data">
                    <div class="col-12">{{ data.item.ciudad }}</div>
                </template>
                <template v-slot:cell(giro)="data">
                    <div class="col-12">{{ data.item.giro }}</div>
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
                                    a_razon_social=data.item.direccion;
                                    a_comuna=data.item.comuna;
                                    a_ciudad=data.item.ciudad;
                                    a_giro=data.item.gito;
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
            tipo_cliente:'',
            razon_social:'',
            comuna:'',
            ciudad:'',
            giro:'',
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
                { key: 'cliente', label: 'Cliente' },
                { key:'tipo', label:'Tipo' },
                { key: 'contacto', label: 'Contacto' },
                { key: 'email', label: 'Email' },
                { key: 'Direccion', label: 'Dirección' },
                { key: 'comuna', label: 'Comuna' },
                { key: 'ciudad', label: 'Ciudad' },
                { key: 'giro', label: 'Giro' },
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
            a_direccion:'',
            a_razon_social:'',
            a_comuna:'',
            a_ciudad:'',
            a_giro:'',

        }
    },
    created(){
        this.listar();
    },
    methods:{

        guardar(){
            this.boton_save=true;
            if(this.validar_campos(this.tipo_cliente) == false){
                alert('Faltan campos por llenar');
                this.boton_save=false;
                return false;
            }else{
                const data = new FormData();
                data.append('rut', document.getElementById('rut').value);
                data.append('nombres', this.nombres);
                data.append('apellidos', this.apellidos);
                data.append('razon_social', this.razon_social);
                data.append('contacto', this.contacto);
                data.append('email', this.email);
                data.append('direccion', this.direccion);

                data.append('comuna', this.comuna);
                data.append('ciudad', this.ciudad);
                data.append('giro', this.giro);
                data.append('tipo_cliente', this.tipo_cliente);

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

        validar_campos(cliente){
            var t = this;
            console.log(cliente)
            if(cliente == 'PERSONA'){
                if(document.getElementById('rut').value.trim()=='' ||
                t.nombres.trim()==''||
                t.apellidos.trim()=='' ||
                t.direccion.trim()=='' ||
                t.comuna.trim()=='' ||
                t.ciudad.trim()=='' ||
                t.giro.trim()=='' ||
                t.tipo_cliente.trim() ==''

                ){
                    return false;
                }else{
                    return true;
                }
            }
            if(cliente == 'EMPRESA'){
                console.log('rut:'+document.getElementById('rut').value.trim());
                console.log('razon_social: '+t.razon_social.trim());
                console.log('direccion: '+ t.direccion.trim());
                console.log('comuna: '+ t.comuna.trim());
                console.log('ciudad: '+t.ciudad.trim());
                console.log('giro: '+ t.giro.trim());
                console.log('tip_cliente: '+t.tipo_cliente.trim());


                if(document.getElementById('rut').value.trim()=='' ||
                t.razon_social.trim()=='' ||
                t.direccion.trim()=='' ||
                t.comuna.trim()=='' ||
                t.ciudad.trim()=='' ||
                t.giro.trim()==''||
                t.tipo_cliente.trim() ==''
                ){
                    return false;
                }else{
                    return true;
                }
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

        formatear_rut(){
            const $rut = document.getElementById('rut').value
            switch ($rut.length) {
                case 9: //xx.xxx.xxx-x
                    console.log("estan xx.xxx.xxx-x")

                    document.getElementById('rut').value = $rut.replace( /^(\d{2})(\d{3})(\d{3})(\w{1})$/, '$1.$2.$3-$4');

                break;
                case 8: //x.xxx.xxx-x
                    console.log("estan x.xxx.xxx-x");

                    document.getElementById('rut').value = $rut.replace( /^(\d{1})(\d{3})(\d{3})(\w{1})$/, '$1.$2.$3-$4');

                break;

                default:
                    break;
            }
            // console.log(this.rut.length == 8);
            // if(this.rut.length == 8){
            //     console.log("cantidad 9")
            //     this.rut = this.rut.replace( /^(\d{2})(\d{3})(\d{3})(\w{1})$/, '$1.$2.$3-$4')
            // }
            // if(this.rut.length == 7){
            //     console.log("cantidad 8")
            //     this.rut = this.rut.replace( /^(\d{1})(\d{3})(\d{3})(\w{1})$/, '$1.$2.$3-$4')
            // }


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
