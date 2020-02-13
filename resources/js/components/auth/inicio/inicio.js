export default {

  data() {

    return {
      productosFields: [
        { key: 'index', label: 'ID',variant: 'dark' },
        { key: 'producto', label: 'Productos' },
        { key: 'cantidad', label: 'Cantidad' },
        { key: 'peso', label: '' },
        { key: 'totalVendido', label: 'Total' },

      ],
      productosItems: [],

      ventasFields: [
        { key: 'index', label: 'ID',variant: 'dark' },
        { key: 'producto', label: 'Productos' },
        { key: 'fecha', label: 'Fecha' },
        { key: 'pesoUnidad', label: '' },
        { key: 'precio', label: 'Unidad' },
        { key: 'cantidad', label: 'Venta' },
        { key: 'peso', label: '' },
        { key: 'totalVendido', label: 'Total' },

      ],
      ventasItems: [],

      cantidadCategorias:'0',
      cantidadProductos:'0',
      totalVentas:'0',
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
        // console.log(this.cantidadCategorias);

      })

    },

    cantidad_productos() {
      this.axios.get('api/cantidad_productos').then((response) => {
        this.cantidadProductos = response.data;

      })

    },
    total_ventas() {
      this.axios.get('api/total_ventas').then((response) => {
        console.log(response);
        this.totalVentas = response.data;

      })

      
    },

    ultimas_ventas() {
      this.axios.get('api/ultimas_ventas').then((response) => {
        console.log(response);
        this.ventasItems = response.data.ventas;

      })

    },

    mas_vendidos() {
      this.axios.get('api/mas_vendidos').then((response) => {
        console.log(response);
        this.productosItems = response.data.vendidos;

      })

    },
  },

mounted() {
    this.cantidad_categorias();
    this.cantidad_productos();
    this.total_ventas();
    this.ultimas_ventas();
    this.mas_vendidos();
  },

}