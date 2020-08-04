import Multiselect from 'vue-multiselect';

export default {
  components: {
    Multiselect
  },

  data() {
    return {
      // REGISTRO PRODUCTO
      categoria_id: null,
      sku:'',
      nombre: '',
      descripcion: '',
      cantidad: '',
      precio_compra: '',
      precio_venta: '',
      listarCategorias: [],

      // ALERTS INGRESO PRODUCTO
      guardar_load:false,
      errores: [],
      correcto: '',
      dismissSecs: 5,
      dismissCountDown: 0,

      selected: 'S',
      options: [
        { item: 'S', name: 'Unidad' },
        { item: 'N', name: 'Granel (Stock - ilimitado)' },
      ]
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
        this.listarCategorias = response.data.cat;
      })

    },

    registrar_producto() {
      
      if (this.categoria_id == null ||
        this.sku.trim() == '' ||
        this.nombre.trim() == '' ||
        this.descripcion.trim() == '' ||
        this.cantidad.trim() == '' ||
        this.precio_compra.trim() == '' ||
        this.precio_venta.trim() == ''){
          this.guardar_load = false;
          alert("faltan campos por llenar");
          return false;
      }

      if (this.precio_compra < 0 || this.precio_venta < 0 || this.cantidad < 0){
        alert("Algunos campos numericos no pueden ser negativos")
        this.guardar_load = false;
        return false;
      }

      this.guardar_load = true;
      const data = {
        'categoria_id': this.categoria_id.id,
        'sku': this.sku,
        'nombre': this.nombre,
        'descripcion': this.descripcion,
        'cantidad': this.cantidad,
        'precio_compra': this.precio_compra,
        'precio_venta': this.precio_venta,
        'stock': this.selected,

      }
      this.axios.post('api/registro_producto', data).then((response) => {
        if (response.data.estado == 'success') {
          this.correcto = response.data.mensaje;
          this.showAlert();
          this.categoria_id = null;
          this.sku = '';
          this.nombre = '';
          this.descripcion = '';
          this.cantidad = '';
          this.precio_compra = '';
          this.precio_venta = '';
          this.errores = [];

          this.guardar_load = false;
        }

        if (response.data.estado == 'failed_v') {
          this.errores = response.data.mensaje;
          this.guardar_load = false;
        }

        if (response.data.estado == 'failed') {
          alert(response.data.mensaje);
          this.guardar_load = false;
        }

      });
    },

  },
  mounted() {
    this.traer_categorias();
  },
}