export default {

  data() {

    return {
      // CABEZERA DE LA TABLA
      ventasFieldsAdm: [
        { key: 'index', label: 'ID' },
        { key: 'prod', label: 'Producto' },
        { key: 'cat', label: 'Categoria' },
        { key: 'desc', label: 'Descripcion' },
        { key: 'ventaUn', label: 'Precio Unidad' },
        { key: 'cant', label: 'Cantidad Vendida' },
        { key: 'venta', label: 'Venta Total' },
        { key: 'fecha', label: 'Fecha Venta' },

      ],

      // LLENAR TABLA
      listarVentas: [],

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
    // ABRIR RUTAS POR CLICK
    url(ruta) {
      this.$router.push({ path: ruta }).catch(error => {
        if (error.name != "NavigationDuplicated") {
          throw error;
        }
      });
    },

    // SUCCESS VENTA CONTADOR
    countDownChanged4(dismissCountDown4) {
      this.dismissCountDown4 = dismissCountDown4;
    },
    showAlert4() {
      this.dismissCountDown4 = this.dismissSecs4;
    },

    traer_ventas() {
      this.axios.get('api/traer_ventas').then((response) => {
        this.listarVentas = response.data.ventas;
      })

    },

    traer_producto() {
      this.listarVentas = [];
      this.axios.get('api/buscar_venta_producto/' + this.buscadorProducto).then((response) => {

        if (this.buscadorProducto == '') {
          this.showAlertBuscar = true;
          this.errorBuscar = ("El campo no puede quedar vacio, ingrese un producto porfavor.");
          this.traer_ventas();
        } else {

          if (response.data.estado == 'success') {

            this.listarVentas = response.data.producto;
            this.buscadorProducto = '';
            this.btn_buscar_producto = true;
          } else {

            if (response.data.estado == 'failed') {
              this.showAlert4();
              this.errores4 = response.data.mensaje;
              console.log(this.errores4);
              this.buscadorProducto = '';
              this.btn_buscar_producto = true;
              this.traer_ventas();
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
    this.traer_ventas();
  },
}