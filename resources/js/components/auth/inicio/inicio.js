export default {

  data() {

    return {
      productosFields: [
        { key: 'index', label: 'ID' },
        { key: 'producto', label: 'Productos' },
        { key: 'totalVendido', label: 'Total Vendido' },

      ],
      productosItems: [
        { producto: 'producto 1', totalVendido: '5000' },
        { producto: 'producto 2', totalVendido: '10000' },
        { producto: 'producto 3', totalVendido: '15000' },
        { producto: 'producto 4', totalVendido: '20000' }
      ],

      ventasFields: [
        { key: 'index', label: 'ID' },
        { key: 'producto', label: 'Productos' },
        { key: 'fecha', label: 'Fecha' },
        { key: 'totalVendido', label: 'Total Vendido' },

      ],
      ventasItems: [
        { producto: 'producto 1', totalVendido: '5000', fecha: '02/01/2020' },
        { producto: 'producto 2', totalVendido: '10000', fecha: '06/01/2020' },
        { producto: 'producto 3', totalVendido: '15000', fecha: '15/01/2020' },
        { producto: 'producto 4', totalVendido: '20000', fecha: '22/01/2020' }
      ],

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
  },

mounted() {
    this.cantidad_categorias();
    this.cantidad_productos();
    this.total_ventas();
  },

}