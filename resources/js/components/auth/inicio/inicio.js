import ChartProductos from './Chart.js'
import ChartVentas from './ChartVentas.js'
export default {
  components: {
    ChartProductos,
    ChartVentas
  },
  data() {

    return {
      productosFields: [
        { key: 'index', label: 'ID', variant: 'dark' },
        { key: 'producto', label: 'Productos' },
        { key: 'cantidad', label: 'Cantidad' },
        { key: 'totalVendido', label: 'Total' },

      ],
      productosItems: [],

      ventasFields: [
        { key: 'index', label: 'ID', variant: 'dark' },
        { key: 'venta', label: 'Total' },
        { key: 'fecha', label: 'Fecha Venta' },
        { key: 'creado', label: 'Venta por' },

      ],
      ventasItems: [],

      cantidadCategorias: '0',
      cantidadProductos: '0',
      totalVentas: '0',
      totalUsuarios: '0',

      //GRAFICO
      datacollection: null,
      datacollection2: null,
    }


  },

  methods: {

    formatPrice(value) {
      let val = (value / 1).toFixed(0).replace('.', ',')
      return val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".")
    },
    
    url(ruta) {
      this.$router.push({ path: ruta }).catch(error => {
        if (error.name != "NavigationDuplicated") {
          throw error;
        }
      });
    },

    cantidad_categorias() {
      this.axios.get('api/cantidad_categoria').then((response) => {
        this.cantidadCategorias = response.data;
      })
    },

    cantidad_productos() {
      this.axios.get('api/cantidad_productos').then((response) => {
        this.cantidadProductos = response.data;
      })
    },

    total_ventas() {
      this.axios.get('api/total_ventas').then((response) => {
        this.totalVentas = response.data;
      })
    },

    ultimas_ventas() {
      this.axios.get('api/ultimas_ventas').then((response) => {
        this.ventasItems = response.data.ventas;
      })
    },

    mas_vendidos() {
      this.axios.get('api/mas_vendidos').then((response) => {
        this.productosItems = response.data.vendidos;
      })
    },

    total_usuarios() {
      this.axios.get('api/allUser').then((response) => {
        this.totalUsuarios = response.data;
      })
    },
    
    mas_vendidos_grafico() {
      this.axios.get('api/mas_vendidos_grafico').then((response) => {
        this.datacollection = response.data;
      })
    },

    ultimas_ventas_grafico() {
      this.axios.get('api/ultimas_ventas_grafico').then((response) => {
        this.datacollection2 = response.data;
      })
    },

  },

  mounted() {
    this.cantidad_categorias();
    this.cantidad_productos();
    this.total_ventas();
    this.ultimas_ventas();
    this.mas_vendidos();
    this.total_usuarios();
    this.mas_vendidos_grafico();
    this.ultimas_ventas_grafico();
  },

}