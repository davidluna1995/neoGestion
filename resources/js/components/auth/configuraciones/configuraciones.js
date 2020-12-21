export default {
    data() {
        return {
            //
            conf_ngreso_caja:false,

            nombre: '',
            email: '',
            password: '',
            passRepetir: '',

            // ALERTS MODIFICAR
            errores: [],
            correcto2: '',
            dismissSecs2: 5,
            dismissCountDown2: 0,

            // PASSWORD ERROR
            errorPassword: '',
            countPassword: false,

            // ERROR BLOQUEAR
            errorBloquear: '',
            countBloquear: false,

            // ALERTS BLOQUEAR
            correcto3: '',
            dismissSecs3: 5,
            dismissCountDown3: 0,

            usuariosFields: [
                { key: 'name', label: 'Nombre Usuario' },
                { key: 'email', label: 'Correo Electronico' },
                { key: 'creado', label: 'Fecha de Incorporacion' },
                { key: 'bloquear', label: '' },

            ],
            listarUsuarios: [],

            selectedRol: null,
            optionsRol: [
                { value: null, text: 'Seleccione una opcion' },
                { value: '1', text: 'Administrador' },
                { value: '2', text: 'Cajera' },
            ],

            logo: '',
            empresa: '',
            direccion: '',
            listarConf: [],
            logoNull: false,

            // ALERTS MODIFICAR
            erroresConf: [],
            correcto: '',
            dismissSecs: 3,
            dismissCountDown: 0,

            //caja
            nombre_caja:'',
            detalle_caja:'',

            get_cajas:[],
            get_editar_caja:[],

            caja_edit_nombre:'',
            caja_edit_descripcion:'',

            get_usuarios:[],
            usuarios_en_caja:[],

            asig_caja:'',
            asig_usuario:'',
            datos_de_caja:[],
            btn_asignar:false,

        }
    },
    methods: {
        formatPrice(value) {
            let val = (value / 1).toFixed(0).replace('.', ',')
            return val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".")
        },

        // MODAL CREAR USUARIO

        abrir_modal(ref){
            this.$refs[''+ref+''].show();
        },
        showModalCrearUsuario() {
            this.$refs['crearUsuario'].show();
        },
        hideModalCrearUsuario() {
            this.$refs['crearUsuario'].hide();
        },
        // MODAL BLOQUEAR USUARIO
        showModalBloquearUsuario() {
            this.$refs['bloquearUsuario'].show();
            this.tabla_usuarios();
        },
        hideModalBloquearUsuario() {
            this.$refs['bloquearUsuario'].hide();
        },
        // MODAL CONFIG
        showModalConf() {
            this.$refs['configuraciones'].show();
            this.traer_configuraciones();
        },
        hideModalConf() {
            this.$refs['configuraciones'].hide();
        },

        contadorUsuarioCreado(dismissCountDown2) {
            this.dismissCountDown2 = dismissCountDown2;
        },
        alertUsuarioCreado() {
            this.dismissCountDown2 = this.dismissSecs2;
        },

        contadorRolUsuario(dismissCountDown3) {
            this.dismissCountDown3 = dismissCountDown3;
        },
        alertRolUsuario() {
            this.dismissCountDown3 = this.dismissSecs3;
        },

        contadorConf(dismissCountDown) {
            this.dismissCountDown = dismissCountDown;
            if (this.dismissCountDown == 0) {
                this.logout();
            }
        },
        alertConf() {
            this.dismissCountDown = this.dismissSecs;
        },

        crear_usuario() {

            const data = {
                'name': this.nombre,
                'email': this.email,
                'rol': this.selectedRol,
                'password': this.password,
                'passRepetir': this.passRepetir,
            }
            this.axios.post('api/crear_usuario', data).then((response) => {
                if (response.data.estado == 'success') {
                    this.correcto2 = response.data.mensaje;
                    this.alertUsuarioCreado();
                    this.nombre = '';
                    this.email = '';
                    this.password = '';
                    this.passRepetir = '';
                    this.errores = [];
                    this.countPassword = false;

                }

                else if (response.data.estado == 'failed_v') {
                    this.errores = response.data.mensaje;
                    this.countPassword = false;
                }

                else if (response.data.estado == 'errorPassword') {
                    this.errorPassword = response.data.mensaje;
                    this.countPassword = true;
                    this.errores = [];

                }
            })
                .catch(error => {
                    alert(error);
                })
        },

        tabla_usuarios() {
            this.axios.get('api/traer_usuarios').then((response) => {
                this.listarUsuarios = response.data;
                // console.log(this.listarUsuarios);
            })

        },

        bloquear_usuario(metodo, id) {
            const data = {
                'metodo': metodo,
                'id': id,
            }
            this.axios.post('api/delete_usuario', data).then((response) => {
                if (response.data.estado == 'success') {
                    this.correcto3 = response.data.mensaje;
                    this.alertRolUsuario();
                    this.countBloquear = false;
                    this.traer_usuarios();

                }

                else if (response.data.estado == 'failed') {
                    this.errorBloquear = response.data.mensaje;
                    this.countBloquear = true;

                }
            })
                .catch(error => {
                    alert(error);
                })
        },

        crear_actualizar_configuraciones() {

            let formData = new FormData();
            formData.append('logo', this.logo[0]);
            formData.append('empresa', this.empresa);
            formData.append('direccion', this.direccion);
            formData.append('rut',  document.getElementById('rut').value);

            this.axios.post('api/registro_configuraciones', formData, {
                headers: {
                    'Content-Type': 'multipart/form-data'
                }
            }).then((response) => {

                if (response.data.estado == 'success') {
                    this.correcto = response.data.mensaje;
                    this.alertConf();
                    this.traer_configuraciones();
                    this.erroresConf = [];
                }

                if (response.data.estado == 'failed_v') {
                    this.erroresConf = response.data.mensaje;
                }

                if (response.data.estado == 'failed') {
                    alert(response.data.mensaje);
                }
            });


        },

        traer_configuraciones() {
            this.axios.get('api/traer_configuraciones').then((response) => {
                if (response.data.estado == 'success') {

                    this.logoNull = true;

                    this.listarConf = response.data.configuraciones;
                    if (this.empresa == null || this.direccion == null) {
                        this.empresa = '';
                        this.direccion = '';
                    } else {
                        this.empresa = this.listarConf.empresa;
                        this.direccion = this.listarConf.direccion;
                    }
                }
            })
        },

        ingresar_caja(){
            this.conf_ngreso_caja = true;
            const data = { 'nombre': this.nombre_caja, 'descripcion': this.detalle_caja};
            this.axios.post('api/ingresar_caja',data).then((res)=>{
                console.log(res.data.estado);
                if(res.data.estado == 'failed'){
                    this.conf_ngreso_caja = false;
                    alert(""+res.data.mensaje+"");
                    return false;
                }

                if(res.data.estado == 'success'){
                    alert(""+res.data.mensaje+"");
                    this.conf_ngreso_caja = false;
                    this.nombre_caja = '';
                    this.detalle_caja = '';
                    this.$refs['modal_caja'].hide();
                }else{
                    this.conf_ngreso_caja = true;
                }
            }).catch(function(error) {
                // this.conf_ngreso_caja = true;
                alert("Algo salio mal, vuelva a revisar sus datos");
              });
        },

        listar_cajas(){
            this.axios.get('api/traer_cajas').then((res) => {
                if(res.data.estado == 'success'){
                    this.get_cajas = res.data.tabla;
                }
            });
        },
        editar_caja(data){
            this.get_editar_caja = [];
            this.get_editar_caja = data;
            this.caja_edit_nombre=data.nombre;
            this.caja_edit_descripcion=data.descripcion;
            this.$refs['modal_editar_caja'].show();


        },

        ver_usuarios_en_caja(data){
            this.usuarios_en_caja = [];
            this.datos_de_caja = data
            this.axios.get('api/ver_usuarios_en_caja/'+data.id).then((res)=>{
                if(res.data.estado == 'success'){
                    this.usuarios_en_caja = res.data.users;
                }
            });

            this.$refs['modal_usuarios_en_caja'].show();
        },

        editar_datos_caja(id){

            const data = {
                'id': id,
                'nombre': this.caja_edit_nombre,
                'descripcion': this.caja_edit_descripcion
            }
            this.axios.post("api/editar_caja", data).then((res) => {
                if(res.data.estado == 'success'){
                    alert(""+res.data.mensaje+"");
                    this.listar_cajas();
                    this.$refs['modal_editar_caja'].hide();

                }else{
                    alert("No se ha podido actualizar la información")
                }
            })
        },
        traer_usuarios(){
           const pet = this.axios.get('api/traer_usuarios');
           pet.then((res)=>{
                this.get_usuarios = res.data;
           });
        }
        ,
        asignar_usuario_a_caja(){
            this.btn_asignar = true;
            const data = {
                'caja_id': this.asig_caja,
                'usuario_id' : this.asig_usuario
            };
            this.axios.post('api/asignar_usuario_a_caja', data).then((res)=>{
                if(res.data.estado == 'success'){
                    alert(res.data.mensaje);
                    this.btn_asignar = false;
                }
                if(res.data.estado == 'failed'){
                    alert(res.data.mensaje);
                    this.btn_asignar = false;
                }
            });
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

        },
        onFileChange(e) {
            this.logo = e.target.files || e.dataTransfer.files;
            console.log(this.logo[0]);
        },

        logout: function () {
            this.$auth.logout({
                makeRequest: true,
                redirect: "/"
            });
        }

    },


    mounted() {

    },
}
