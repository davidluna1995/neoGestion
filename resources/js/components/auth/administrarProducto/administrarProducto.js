import Multiselect from 'vue-multiselect';

export default {

    components: {
        Multiselect
    },

    data() {

        return {

            transProps: {
                // Transition name
                name: 'flip-list'
              },
            // CABEZERA DE LA TABLA
            productosFieldsAdm: [
                { key: 'index', label: 'ID', variant: 'dark'},
                { key: 'prod', label: 'Producto' },
                { key: 'cat', label: 'Categoria' },
                { key: 'desc', label: 'Descripcion' },
                { key: 'stock', label: 'Stock' },
                { key: 'compra', label: 'Compra' },
                { key: 'venta', label: 'Venta',},
                { key: 'fecha', label: 'Creado' },
                { key: 'editar', label: '' },
                { key: 'ventaModal', label: '' },
                { key: 'eliminarProd', label: '' },

            ],

            // LLENAR TABLA
            listarProductos: [],
            listarCategorias: [],

            // ALERTS MODIFICAR
            errores2: [],
            correcto2: '',
            dismissSecs2: 5,
            dismissCountDown2: 0,

            //ACTUALIZAR
            nombreUpd: '',
            descripcionUpd: '',
            categoria_id: null,
            cantidadUpd: '',
            precioCompraUpd: '',
            precioVentaUpd: '',

            // REGISTRO DE VENTA
            producto_id: '',
            cantidad: '',
            ventas: '',

            // ALERTS INGRESO VENTA
            errores3: [],
            correcto3: '',
            dismissSecs3: 5,
            dismissCountDown3: 0,

            // ALERT STOCK PRODUCTO
            errorStock: '',
            showAlertStock: false,

            // BUSCADOR
            buscadorProducto: '',
            productoSearch: '',
            idProducto: '0',
            btn_buscar_producto: true,

            // ALERTS BUSCADOR PRODUCTOS
            errores4: [],
            correcto4: '',
            dismissSecs4: 5,
            dismissCountDown4: 0,
            showAlertBuscar: false,
            errorBuscar: '',
        }
    },

    methods: {

        formatPrice(value) {
            let val = (value / 1).toFixed(0).replace('.', ',')
            return val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".")
        },
        // BUSCADOR SELECT CATEGORIA
        buscadorCategorias({ descripcion }) {
            return `${descripcion}`
        },
        // ABRIR RUTAS POR CLICK
        url(ruta) {
            this.$router.push({ path: ruta }).catch(error => {
                if (error.name != "NavigationDuplicated") {
                    throw error;
                }
            });
        },
        // MODAL VENTAS
        showModal(id) {
            this.producto_id = id;
            this.$refs['ventasModal' + id].show();
        },
        hideModal(id) {
            this.$refs['ventasModal' + id].hide();
        },
        // MODAL EDITAR
        showModal2(id) {
            this.$refs['editarModal' + id].show();
        },
        hideModal2(id) {
            this.$refs['editarModal' + id].hide();
        },
        // SUCCESS EDITAR CONTADOR
        countDownChanged2(dismissCountDown2) {
            this.dismissCountDown2 = dismissCountDown2;
        },
        showAlert2() {
            this.dismissCountDown2 = this.dismissSecs2;
        },
        // SUCCESS VENTA CONTADOR
        countDownChanged3(dismissCountDown3) {
            this.dismissCountDown3 = dismissCountDown3;
        },
        showAlert3() {
            this.dismissCountDown3 = this.dismissSecs3;
        },
        // SUCCESS VENTA CONTADOR
        countDownChanged4(dismissCountDown4) {
            this.dismissCountDown4 = dismissCountDown4;
        },
        showAlert4() {
            this.dismissCountDown4 = this.dismissSecs4;
        },
        
        traer_productos() {
            this.axios.get('api/traer_productos').then((response) => {
                this.listarProductos = response.data.productos;
            })

        },

        traer_categorias() {
            this.axios.get('api/traer_categorias').then((response) => {
                this.listarCategorias = response.data.cat;
            })

        },

        actualizar_dato(id, campo, input) {
            const data = {
                'id': id,
                'campo': campo,
                'input': input,
            }
            this.axios.post('api/modificar_campo_producto', data).then((response) => {
                if (response.data.estado == 'success') {
                    this.correcto2 = response.data.mensaje;
                    this.showAlert2();
                    this.traer_productos();
                    this.hideModal2(id);
                    this.nombreUpd = '';
                    this.descripcionUpd = '';
                    this.categoria_id = null;
                    this.cantidadUpd = '';
                    this.precioCompraUpd = '';
                    this.precioVentaUpd = '';
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
                    this.loading = false;
                })
        },

        registrar_venta() {
            const data = {
                'producto_id': this.producto_id,
                'cantidad': this.cantidad,
                'venta': this.ventas,
            }
            this.axios.post('api/registro_venta', data).then((response) => {
                if (response.data.estado == 'success') {
                    this.correcto3 = response.data.mensaje;
                    this.showAlert3();
                    this.hideModal(this.producto_id);
                    this.traer_productos();
                    this.producto_id = '';
                    this.cantidad = '';
                    this.ventas = '';
                    this.showAlertStock = false;
                    this.errores3 = [];
                }

                if (response.data.estado == 'failed') {
                    this.showAlertStock = true;
                    this.errorStock = response.data.mensaje;
                }
                if (response.data.estado == 'failed_v') {
                    this.errores3 = response.data.mensaje;
                    return false;
                }

            });
        },

        traer_producto() {
            this.listarProductos = [];
            this.axios.get('api/buscar_producto/' + this.buscadorProducto).then((response) => {

                if (this.buscadorProducto == '') {
                    this.showAlertBuscar = true;
                    this.errorBuscar = ("El campo no puede quedar vacio, ingrese un producto porfavor.");
                    this.traer_productos();
                } else {

                    if (response.data.estado == 'success') {

                        this.listarProductos = response.data.producto;
                        this.buscadorProducto = '';
                        this.btn_buscar_producto = true;
                    } else {

                        if (response.data.estado == 'failed') {
                            this.showAlert4();
                            this.errores4 = response.data.mensaje;
                            console.log(this.errores4);
                            this.buscadorProducto = '';
                            this.btn_buscar_producto = true;
                            this.traer_productos();
                        }
                    }
                }

            })
                .catch(error => {
                    alert(error);
                })
        },

        escribiendoProducto() {
            if (this.buscadorProducto.toLowerCase().trim() == '') {
                this.btn_buscar_producto = true;
            } else {

                this.btn_buscar_producto = false;
            }
        },

    },

    mounted() {
        this.traer_productos();
        this.traer_categorias();
    },
}