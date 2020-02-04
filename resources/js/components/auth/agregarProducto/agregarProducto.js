import Multiselect from 'vue-multiselect';

export default {
  components: {
    Multiselect
  },

  data() {
    return {
      // REGISTRO PRODUCTO
      categoria_id: null,
      nombre: '',
      descripcion: '',
      cantidad: '',
      precio_compra: '',
      precio_venta: '',
      fecha: '',
      hora: '',
      listarCategorias: [],

      // ALERTS INGRESO PRODUCTO
      errores: [],
      correcto: '',
      dismissSecs: 5,
      dismissCountDown: 0,
    }

  },

  methods: {

    buscadorCategorias({ descripcion }) {
      return `${descripcion}`
    },

    url(ruta) {
      this.$router.push({ path: ruta }).catch(error => {
        if (error.name != "NavigationDuplicated") {
          throw error;
        }
      });
    },

    countDownChanged(dismissCountDown) {
      this.dismissCountDown = dismissCountDown
    },
    showAlert() {
      this.dismissCountDown = this.dismissSecs
    },

    traer_categorias() {
      this.axios.get('api/traer_categorias').then((response) => {
        this.listarCategorias = response.data;
      })

    },

    registrar_producto() {
      const data = {
        'categoria_id': this.categoria_id.id,
        'nombre': this.nombre,
        'descripcion': this.descripcion,
        'cantidad': this.cantidad,
        'precio_compra': this.precio_compra,
        'precio_venta': this.precio_venta,
        'fecha': this.fecha,
        'hora': this.hora,

      }
      this.axios.post('api/registro_producto', data).then((response) => {
        if (response.data.estado == 'success') {
          this.correcto = response.data.mensaje;
          this.showAlert();
          this.categoria_id = null;
          this.nombre = '';
          this.descripcion = '';
          this.cantidad = '';
          this.precio_compra = '';
          this.precio_venta = '';
          this.fecha = '';
          this.hora = '';
          this.errores = [];
        }

        if (response.data.estado == 'failed_v') {
          this.errores = response.data.mensaje;
        }

        if (response.data.estado == 'failed') {
          alert(response.data.mensaje);
        }

      });
    },

  },
  mounted() {
    this.traer_categorias();
  },
}