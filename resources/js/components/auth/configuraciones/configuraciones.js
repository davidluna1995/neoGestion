export default {
    data() {
        return {
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

        }
    },
    methods: {
        formatPrice(value) {
            let val = (value / 1).toFixed(0).replace('.', ',')
            return val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".")
        },

        // MODAL CREAR USUARIO
        showModalCrearUsuario() {
            this.$refs['crearUsuario'].show();
        },
        hideModalCrearUsuario() {
            this.$refs['crearUsuario'].hide();
        },
        // MODAL BLOQUEAR USUARIO
        showModalBloquearUsuario() {
            this.$refs['bloquearUsuario'].show();
            this.traer_usuarios();
        },
        hideModalBloquearUsuario() {
            this.$refs['bloquearUsuario'].hide();
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

        traer_usuarios() {
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
        }
    },


    mounted() {

    },
}