export default {
    data() {
        return {

            buscadorProducto: '',
            montoEfectivo:'',
            montoDebito:'',
            montoCredito:'',

            efectivo: false, 
            debito: false,
            credito: false,

            // CABEZERA DE LA TABLA
            productosFieldsAdm: [
                { key: 'cantidad', label: 'Cantidad', thStyle: { color: 'white' } },
                { key: 'detalle', label: 'Detalle' },
                { key: 'precioProd', label: 'Precio Producto' },
                { key: 'subtotal', label: 'Sub Total' },
                { key: 'opc', label: 'Eliminar' },

            ],

            // LLENAR TABLA
            listarProductos: [
                { cantidad: '1', detalle: 'iphone de klida paquepo', precioProd: '600', subtotal: '600' }
            ],

            formaPago: [],
            formaPagoOpcion: [
                { text: 'Efectivo', value: '1' },
                { text: 'T.Debito', value: '2' },
                { text: 'T.Credito', value: '3' },
            ],

            entrega: [],
            entregaOpcion: [
                { text: 'Entrega Inmediata', value: '1' },
                { text: 'Por Despachar', value: '2' },
            ]

        }
    },

    methods: {

        // MODAL VENTAS
        showModal() {
            this.$refs['ventasModal'].show();
        },
        hideModal() {
            this.$refs['ventasModal'].hide();
        },

    },

    mounted() {

    },
}