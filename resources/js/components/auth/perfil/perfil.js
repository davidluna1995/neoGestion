export default {
    data() {
        return {

            // ALERTS MODIFICAR
            errores2: [],
            correcto2: '',
            dismissSecs2: 5,
            dismissCountDown2: 0,

            //ACTUALIZAR
            nameUpd: '',
            emailUpd: '',

            passActual: '',
            passNueva: '',
            passRepetir: '',
        }
    },
    methods: {
        formatPrice(value) {
            let val = (value / 1).toFixed(0).replace('.', ',')
            return val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".")
        },

        countDownChanged2(dismissCountDown2) {
            this.dismissCountDown2 = dismissCountDown2;
            if (this.dismissCountDown2 == 0) {
                this.logout();
            }
        },
        showAlert2() {
            this.dismissCountDown2 = this.dismissSecs2;
        },

        actualizar_dato(campo, input) {
            const data = {
                'campo': campo,
                'input': input,
            }
            this.axios.post('api/modificar_perfil', data).then((response) => {
                if (response.data.estado == 'success') {
                    this.correcto2 = response.data.mensaje;
                    this.showAlert2();
                    this.nameUpd = '';
                    this.emailUpd = '';
                    this.errores2 = [];
                }
                if (response.data.estado == 'failed_v') {
                    this.errores2 = response.data.mensaje;
                }

                if (response.data.estado == 'failed') {
                    alert(response.data.mensaje);
                    this.campoUpd = '';
                }
            })
                .catch(error => {
                    alert(error);
                })
        },

        actualizar_password() {

            if (this.passNueva == this.passRepetir) {

                const data = {
                    'password_actual': this.passActual,
                    'password_nueva': this.passNueva,
                }
                this.axios.post('api/cambiar_password', data).then((response) => {
                    if (response.data.estado == 'success') {
                        this.correcto2 = response.data.mensaje;
                        this.showAlert2();
                        this.passActual = '';
                        this.passNueva = '';
                        this.passRepetir = '';
                        this.errores2 = [];
                    }

                    if (response.data.estado == 'failed') {
                        alert(response.data.mensaje);
                        this.passActual = '';
                        this.passNueva = '';
                        this.passRepetir = '';
                    }
                })
                    .catch(error => {
                        alert(error);
                    })

            } else{
                alert("Las contraseñas no coinciden.")
            }

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