import XLSX from 'xlsx'
export default {
    data() {


        return {
            usuario: this.$auth.user(),
            admin:1,
            printVenta: {
                id: "printVenta",
                popTitle: 'good print',
                extraCss: 'https://www.google.com,https://www.google.com',
                extraHead: '<meta http-equiv="Content-Language"content="zh-cn"/>'
            },
            printDetalle: {
                id: "printDetalle",
                popTitle: 'good print',
                extraCss: 'https://www.google.com,https://www.google.com',
                extraHead: '<meta http-equiv="Content-Language"content="zh-cn"/>'
            },


            desde: '',
            hasta: '',
            hora_h:'23:59',
            hora_d:'00:00',
            suma_ventas:0,
            vuelto:0,
            credito:0,
            filtro:false,
            resumen_titulo:'',
            efectivo_real:0,
            debito:0,
            btn_filtrar:false,

            // CABEZERA DE LA TABLA VENTAS
            reporteVentasFieldsAdm: [
                { key: 'index', label: 'ID', variant: 'dark' },
                { key: 'venta', label: 'Venta Total' },
                { key: 'fecha', label: 'Fecha Venta' },
                { key: 'dte', label: 'Doc.' },
                { key: 'creado', label: 'Creado Por' },
                { key: 'cliente', label: 'Cliente' },
                { key: 'tipo_pago', label:'Tipo de pago' },
                { key: 'credito', label:'Credito' },
                { key: 'vuelto', label:'Vuelto' },
                { key: 'detalle', label: 'Detalle' },
                { key: 'comprobante', label: 'Comprobante' },


            ],
            // LLENAR TABLA VENTAS
            listarReporteVentas: [],

            // CABEZERA DE LA TABLA DETALLE VENTA
            reporteDetalleVentaFieldsAdm: [
                { key: 'imagen', label: 'Imagen' },
                { key: 'nombre', label: 'Nombre producto' },
                { key: 'descripcion', label: 'Descripcion' },
                { key: 'categoria', label: 'Categoria' },
                { key: 'unidad', label: 'Unidad' },
                { key: 'precio', label: 'Precio' },
                { key: 'cantidad', label: 'Cantidad Vendida' },
                { key: 'descuento', label: '$ Descuento' },
                { key: '_descuento', label: '% Descuento' },
                { key: 'impuesto_adicional', label: 'Imp adicional' },
                { key: 'tipo_impuesto_adicional', label: 'ID Imp adicional' },
                { key: 'afecto', label: 'Afecto' },



            ],
            // LLENAR TABLA DETALLE VENTAS
            listarReporteDetalleVentas: [],

            dataToExport: [
                {
                  nombre: 'Jorge',
                  ocupacion: 'Best Admin'
                },
                {
                  nombre: 'Héctor',
                  ocupacion: 'Worst Admin'
                },
                {
                  nombre: 'gmq',
                  ocupacion: ':shrug:'
                }
            ],


            //variables del comprobante//--------
            logoNull:false,
            listarConf:[],
            ticketPrint:[],
            ticketPrintDetalle:[],
            load_comprobante:false,

            //datos pa visualizar factura
            factura:{
                Productos: [
                  {
                    // id: 0,
                    Afecto: "",
                    nomNombreProducto: "",
                    DescripcionAdicional: "",
                    UnidadMedida: 0,
                    Cantidad: 0,
                    PrecioNeto:0,
                    DescuentoNeto:0,
                    SubTotal:0,
                    TipoImpAdicional:0,
                    MontoImpAdicional:0
                  }

                ],
                venta_total: 0,
                forma_pago_id: "",
                tipo_entrega_id: "",
                Cliente: {
                    id: 0,
                    Rut: "",
                    tipo_cliente: "",
                    RazonSocial: "",
                    Contacto: "",
                    Email: "",
                    Direccion: "",
                    Comuna: "",
                    Ciudad: "",
                    Giro: ""
                },
                pago_efectivo: "",
                pago_debito: null,
                chk_credito: false,
                detalle_credito: null,
                monto_credito: 0,
                sii_forma_pago: "",
                tipo_venta_id: 33,
                emisor: {
                  id: 1,
                  logo: "",
                  empresa: "",
                  direccion: "",
                  deleted_at: null,
                  created_at: "",
                  updated_at: "",
                  rut: "",
                  giro: ""
                }
            },
            totales:{},
            total: 0,
            impuesto_especifico:'0',
            suma_solo_ivas:0
            //fin datos pa visualizar factura
        }
    },
    methods: {

        exportExcel: function () {
            // let data = XLSX.utils.json_to_sheet(this.dataToExport)
            // const workbook = XLSX.utils.book_new()
            // const filename = 'devschile-admins'
            // XLSX.utils.book_append_sheet(workbook, data, filename)
            // XLSX.writeFile(workbook, `${filename}.xlsx`)
            var tbl = document.getElementById('sheetjs');
            var wb = XLSX.utils.table_to_book(tbl);
            console.log(wb)

        },
        s2ab(s){
            var buf = new ArrayBuffer(s.length);
            var view = new Uint8Array(buf);
            for(var i=0; i<s.length;i++) view[i] = s.charCodeAt(i) & 0xFF;
                return buf

        },
        exportar_tabla: function(json_tabla){

            var wb = XLSX.utils.book_new();
            wb.SheetNames.push("Test sheet1");

            var ws_data = [['hola','mundo','como tas']];
            // var ws = XLSX.utils.aoa_to_sheet(ws_data);
            var ws = XLSX.utils.table_to_sheet(document.getElementById('cabeza'))
            wb.Sheets["Test sheet1"] = ws;


            var ws2 = XLSX.utils.table_to_sheet(document.getElementById('tabla_excel'))
            wb.SheetNames.push("Test sheet2");
            wb.Sheets["Test sheet2"] = ws2;

            console.log(wb);
            // var wbout = XLSX.write(wb, {bookType:'xlsx', bookSST:true, type:'binary'});

            XLSX.writeFile(wb, `test.xlsx`,{ flag: 'w+' })
            // saveAs(new Blob([this.s2ab(wbout)],{type:'application/octet-stream'}), 'test.xlsx')

            // var Heading = [
            //     ["ID", "VENTA TOTAL", "FECHA VENTA", "CREADO POR", "CLIENTE", "TIPO DE PAGO", "CREDITO", "VUELTO"],
            //   ];
            // var  tbl  = document.getElementById( 'printVenta') ;
            // var  tbl2  = document.getElementById( 'printVenta') ;
            // let data = XLSX.utils.table_to_sheet(tbl)
            // let data2 = XLSX.utils.table_to_book(tbl2)
            // data['A1'].v = 'ID VENTA'
            // data['B1'].v = 'FECHA'
            // data['C1'].v = 'VENTA TOTAL'
            // data['D1'].v ='NOMBRE USUARIO'
            // const workbook = XLSX.utils.book_new()
            // workbook.Props = {
            //     Title:'Reporte venta',
            //     Author:'NF'
            // };
            // const filename = 'devschile-admins'
            // XLSX.utils.book_append_sheet(workbook, data, 'sheet1')
            // XLSX.utils.book_append_sheet(workbook, data2, 'sheet2')

            // XLSX.writeFile(workbook, `${filename}.xlsx`,{ flag: 'w+' })
        },

        formatPrice(value) {
            let val = (value / 1).toFixed(0).replace('.', ',')
            return val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",")
        },

        // MODAL EDITAR
        abrir_venta(ref, venta_id){
            this.limpia_factura();
            this.load_comprobante = true;
            this.axios.get('api/comprobante/'+venta_id).then((res)=>{

                if(res.data.estado == 'success'){
                    if(res.data.venta.tipo_venta_id == 1){
                        this.listarConf = res.data.configuraciones;
                        this.ticketPrint = res.data.venta;
                        this.ticketPrintDetalle = res.data.venta_detalle
                        this.totales = res.data.venta;
                        this.$refs[""+ref+""].show();
                        this.load_comprobante = false;
                    }
                    if(res.data.venta.tipo_venta_id == 33){
                        this.factura = res.data.factura;
                        this.totales = res.data.venta;
                        this.$refs[""+ref+""].show();
                        this.load_comprobante = false;
                    }
                }else{
                    this.load_comprobante = false;
                    alert("No es posible seguir con el proceso");
                }


            });

        },
        showModalDetalleVenta(id, idVenta) {
            this.$refs['reporte' + id].show();
            this.traer_detalle_ventas(idVenta);
        },
        hideModalDetalleVenta(id, idVenta) {
            this.$refs['reporte' + id].hide();
            this.traer_detalle_ventas(idVenta);
        },

        traer_ventas() {
            this.btn_filtrar = true;
            if(this.usuario.rol != this.admin){
                return false;
            }
            this.filtro = false;
            if (this.desde == '' || this.hasta == '' || this.hora_d =='' || this.hora_h=='') {
                this.filtro = false;
                this.btn_filtrar = false;
                alert("seleccione un rango de fechas.");
                return false;
            } else {
                this.listarReporteVentas = [];
                this.axios.get('api/reporte_ventas/' + this.desde+' '+this.hora_d + '/' + this.hasta+' '+this.hora_h).then((response) => {
                    if (response.data.estado == 'success') {
                        this.listarReporteVentas = response.data.ventas;
                        this.suma_ventas = response.data.total;
                        // this.credito = response.data.deuda;
                        this.vuelto = response.data.vuelto;
                        this.resumen_titulo = response.data.fecha;
                        this.filtro = true;
                        this.efectivo_real = response.data.efectivo_real;
                        this.debito = response.data.debito;
                        this.credito = response.data.credito;
                        this.btn_filtrar = false;
                    }
                    if (response.data.estado == 'failed') {
                        this.filtro = false;
                        this.btn_filtrar = false;
                        alert(response.data.mensaje);
                    }
                })
            }

        },
        limpia_factura(){
            this.totales = {};
            this.factura={
                Productos: [
                    {
                      id: 0,
                      Afecto: "",
                      nomNombreProducto: "",
                      DescripcionAdicional: "",
                      UnidadMedida: 0,
                      Cantidad: "",
                      PrecioNeto:0,
                      DescuentoNeto:0,
                      SubTotal:0,
                      TipoImpAdicional:0,
                      MontoImpAdicional:0
                    }

                  ],
                venta_total: 0,
                forma_pago_id: "",
                tipo_entrega_id: "",
                Cliente: {
                  id: 0,
                  Rut: "",
                  tipo_cliente: "",
                  RazonSocial: "",
                  Contacto: "",
                  Email: "",
                  Direccion: "",
                  Comuna: "",
                  Ciudad: "",
                  Giro: ""
                },
                pago_efectivo: "",
                pago_debito: null,
                chk_credito: false,
                detalle_credito: null,
                monto_credito: 0,
                sii_forma_pago: "",
                tipo_venta_id: 33,
                emisor: {
                  id: 1,
                  logo: "",
                  empresa: "",
                  direccion: "",
                  deleted_at: null,
                  created_at: "",
                  updated_at: "",
                  rut: "",
                  giro: ""
                }
            };
        },
        traer_detalle_ventas(idVenta) {
            if(this.usuario.rol != this.admin){
                return false;
            }
            this.listarReporteDetalleVentas = [];
            this.axios.get('api/traer_detalle_venta/' + idVenta).then((response) => {
                this.listarReporteDetalleVentas = response.data.detalleVenta;

            })

        },

        limpiar() {
            this.desde = '';
            this.hasta = '';
            this.listarReporteVentas = [];
            this.listarReporteDetalleVentas = [];
            this.suma_ventas = 0;
            // this.credito =0;
            this.vuelto = 0;
            this.filtro = false;
        }

    },
    mounted() {
    },
}
