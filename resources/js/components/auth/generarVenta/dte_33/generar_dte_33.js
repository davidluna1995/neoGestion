import Multiselect from 'vue-multiselect';
import json_tipos_imp_ad from './tipos_impuestos_adicionales.json'

export default {
  components: {
    Multiselect
  },
    data() {
        return {

            // BUSCADOR
            view_buscando:false,
            lista_buscando:[],
            buscando_txt:'',

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

            // FORMA DE PAGO
            cliente_id: '',
            efectivo: false,
            debito: false,
            credito: false,
            montoEfectivo: 0,
            montoDebito: 0,
            montoCredito: 0,
            montoEfectivoDebito: 0,
            total: 0,
            totalTemporal: 0,

            chk_credito: false,
            detalle_credito:'',
            monto_credito:0,

            confirm_compra:false,


            arregloCarro: [],
            cantidadStock: '',

            listar_clientes: [],
            cliente:'',
            impuesto_especifico:'0',
            total_a_pagar:0,
            cliente_paga:0,
            // CABEZERA DE LA TABLA AGREGAR PRODUCTO
            carroFieldsAdm: [
                { key: 'sku', label: 'SKU' },
                { key: 'cantidad', label: 'Cantidad' },
                { key: 'unidad', label: 'unidad' },
                { key: 'producto', label: 'Producto' },
                { key: 'precioProd', label: 'Precio Producto' },
                { key: 'afecto', label: 'Afecto I.V.A' },
                { key: 'tia', label: 'Tipo impuesto adicional' },
                { key: 'mia', label: 'Monto impuesto adicional' },
                { key: 'afecto', label: 'Afecto I.V.A' },
                { key: 'descuento', label: '% Descuento' },
                { key: 'subtotal', label: 'Sub Total' },
                { key: 'opc', label: 'Eliminar' },

            ],

            // LLENAR TABLA
            listarCarro: [],

            // CABEZERA DE LA TABLA AGREGADOS
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
                // { text: 'T.Credito', value: '3' },
            ],

            entrega: [],
            entregaOpcion: [
                { text: 'Entrega Inmediata', value: '1' },
                { text: 'Por Despachar', value: '2' },
            ],

            // ALERTS BUSCADOR PRODUCTOS
            errores4: [],
            correcto4: '',
            dismissSecs4: 3,
            dismissCountDown4: 0,
            showAlertBuscar: false,
            errorBuscar: '',

            // ALERTS PRODUCTO REPETIDO
            dismissSecs5: 5,
            dismissCountDown5: 0,

            // CAMPOS VACIOS O STOCK MAYOR
            dismissSecs6: 5,
            dismissCountDown6: 0,
            errores6: [],

            // ALERTS INGRESO VENTA
            errores3: [],
            correcto3: '',
            dismissSecs3: 5,
            dismissCountDown3: 0,

            fechaLocal: '',
            horaLocal: '',
            usuario: this.$auth.user(),

            printVenta: {
                id: "printVenta",
                popTitle: 'good print',
                extraCss: 'https://www.google.com,https://www.google.com',
                extraHead: '<meta http-equiv="Content-Language"content="zh-cn"/>'
            },

            ticketPrint: [],
            ticketPrintDetalle: [],
            listarConf: [],
            logoNull: false,
            get_vuelto:0,

            //datos para la factura
            ted:'',
            traer_ul_venta:false,
            // local_storage_venta:(localStorage.getItem('venta_id')) ? localStorage.getItem('venta_id') :'',


            listarConf:{

            },
            emisor:{
                created_at: "2020-10-21 10:30:36",
                deleted_at: null,
                direccion: "quito 1120, villa pto alegres",
                dirreccion: "",
                empresa: "NEOGESTION",
                giro: "GIRO DE SERVICIOS DE PAGINAS WEB",
                id: 1,
                logo: "storage/ArchivosConfiguracion/fox-head_1606833571.ico",
                rut: "77.106.553-8",
                updated_at: "2020-12-29 12:42:29",
            },
            ticketPrint:{},
            ticketPrintDetalle:{},


            mostrar_cliente:[],
            ver_cliente:false,
            btn_buscar_rut:false,

            // imp_ad:'0',
            tipos_imp_adicionales:json_tipos_imp_ad,

            sii_forma_pago:'',
            visualizar_compra:false,
            pre_factura:{
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
            suma_solo_ivas:0, //afectos
            suma_solo_exento:0, //exentos
            date:{},

            //PERIODO Y CAJAS

            estado_periodo:'',
            estado_caja:'',
            nombre_caja:'',
            // FIN PERIODO Y CAJAS

            //variables para abrir periodo y caja
            fecha_inicio: '',
            hora_inicio: '',
            apertura_monto: 0,
            //fin variables para abrir periodo y caja

            //variables para cerrar periodo y o caja
            data_caja_periodo:[],
            capta_monto:{},

            cierre_monto:0,
            btn_cerrar_caja:false,
            btn_abrir_caja:false,
            btn_cerrar_periodo:false,
            get_datos_periodo:[],

            local_storage_venta:(localStorage.getItem('fac_venta_id')) ? localStorage.getItem('fac_venta_id') :'',
            redon_medio_pago:'DEBITO', //para obtener monto real
            dte_precio:'iva_incluido',

            post_factura:{},
            fac_venta:{},
            fac_cliente:{},
            texto_monto_bruto:'',

        }


    },

    created() {

        console.log(document);
        setInterval(this.getNow, 1000);
        console.log(document.getElementsByName('vuelto'));

        console.log("pura caca vue");


        // this.total_a_pagar = document.getElementById('total_a_pagar').value;
    },



    methods: {

        fecha_hora_acual(){
                this.axios.get('api/fecha_hora_actual').then((res)=>{
                    this.date = res.data
                });
        },

        david_kk(){
            if( this.formaPago.length == 0){
                this.montoDebito = 0

                this.montoEfectivo = 0;
            }
            if(this.sii_forma_pago.trim() =='CONTADO' && this.formaPago.length == 1){
                console.log(this.formaPago);
                //si esta seleccionado solo efectivo, entonces
                if(this.formaPago[0] == '1'){
                    console.log("seleccionaste solo efectivo");
                    this.montoDebito = 0
                    this.redon_medio_pago = 'EFECTIVO';

                }

                //si esta seleccionado solo efectivo, entonces
                if(this.formaPago[0] == '2'){
                    console.log("seleccionaste solo debito");
                    this.montoEfectivo = 0;
                    this.redon_medio_pago = 'DEBITO';


                }

            }
            // o si aparte de estar en contado pero "ambos" tipos seleccionado
            if(this.sii_forma_pago.trim() =='CONTADO' && this.formaPago.length == 2){
                //si esta seleccionado  efectivo y debito entonces
                this.redon_medio_pago = 'DEBITO' // quiere decir que puede pagar ene fectivo un monto
                //razonable evitando monedas de 1 o 5, pero en el momento de pagar con debito que cancele lo correspondiente

            }
        },
        limpia_factura(){
            this.pre_factura={
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
                },

            };
        },

        buscando_personalizado(){

                        this.view_buscando = false;

                        this.lista_buscando = [];

                        if(this.buscando_txt.trim() == ''){

                            this.view_buscando = false;

                            this.lista_buscando = [];

                            this.axios.get('api/users/autocomplete/none').then((response) => {

                                this.view_buscando = false;

                                console.log(response)

                                 this.lista_buscando = response.data;

                            });

                        }else{

                            this.axios.get('api/users/autocomplete/'+this.buscando_txt).then((response) => {

                                this.view_buscando = true;

                                console.log(response)

                                 this.lista_buscando = response.data;

                            });

                        }





                    },


        traer_clientes() {
            this.axios.get('api/listar_clientes').then((response) => {
              this.listar_clientes = response.data.cuerpo;
                    })

          },

        buscadorClientes({ nombres,apellidos,rut }) {
            return `${nombres} ${apellidos} - ${rut}`
          },

        // MODAL VENTAS
        abrir_modal(ref){
            this.$refs[""+ref+""].show();
        },
        showModal() {
            this.$refs['ventasModal'].show();
        },
        hideModal() {
            this.$refs['ventasModal'].hide();
            // this.limpiarCarro();
        },

        redondeo(medio_pago,numero){
            // console.log('numero_pelao',Math.round(numero));
            const parsear = Math.round(numero);
            console.log('monto',parsear);
            if(medio_pago == 'EFECTIVO'){

                const max = (''+parsear).length;
                console.log("max", max)
                const ultimo_numero = (''+parsear)[max - 1];
                console.log('ultomo_numero', ultimo_numero);
                if(ultimo_numero >= 0 && ultimo_numero <= 5){
                    //entonces redondear para abajo
                   return numero - ultimo_numero
                }
                if(ultimo_numero >= 6){
                    return numero + (10 - ultimo_numero);
                }

                return last;

            }

            if(medio_pago == 'DEBITO'){
                return numero;
            }


        },


        // uppercase: function(v) {
        //     return v.toUpperCase();
        // },
        fecha_espaniol(date){
            return date.getMonth();
        },
        visualizar_factura(cliente){

            if(this.estado_caja=='INACTIVO'){
                this.$refs['modal-periodo-caja'].show();
                return false;
            }
            this.limpia_factura();
            this.visualizar_compra = true;


            //Validamos que el cliente / empresa se esten renderizando en el div para continuar
            if(this.ver_cliente == false){
                alert("El cliente no se esta buscando");
                this.visualizar_compra = false;
                return false;
            }

            if(this.sii_forma_pago.trim()==''){
                alert("Falta forma de pago de la factura");
                this.visualizar_compra = false;
                 return false;
            }
            if (document.getElementById('rut').value.trim() == ''){

                alert("Falta el cliente");
                this.visualizar_compra = false;
                 return false;
            }
            // si se selecciono "contado" pero el tipo de pago no se ha seleccionado nada entonces validar
            if(this.sii_forma_pago.trim() =='CONTADO' && this.formaPago.length == 0){
                this.visualizar_compra = false;
                alert("No ha seleccionado un tipo de pago")
                return false;
            }
            console.log("carro", this.arregloCarro.length)
            if(this.arregloCarro.length == 0){
                this.visualizar_compra = false;
                alert("El carro esta vacio")
                return false;
            }
            // o si aparte de estar en contado hay algun tipo de pago seleccionado, entonces ver cual esta activo para asi validar
            if(this.sii_forma_pago.trim() =='CONTADO' && this.formaPago.length == 1){
                console.log(this.formaPago);
                //si esta seleccionado solo efectivo, entonces
                if(this.formaPago[0] == '1'){
                    // console.log("seleccionaste solo efectivo");
                    if(this.montoEfectivo =='' || this.montoEfectivo <= 0){

                        alert("El monto efectivo debe existir o ser mayor a cero")
                        this.visualizar_compra = false;
                        return false
                    }
                }

                //si esta seleccionado solo efectivo, entonces
                if(this.formaPago[0] == '2'){
                    // console.log("seleccionaste solo debito");
                    if( this.montoDebito <= 0){
                        alert("El monto debito debe existir o ser mayor a cero")
                        this.visualizar_compra = false;
                        return false
                    }
                }

            }

            // o si aparte de estar en contado pero "ambos" tipos seleccionado
            if(this.sii_forma_pago.trim() =='CONTADO' && this.formaPago.length == 2){
                //si esta seleccionado  efectivo y debito entonces
                if((this.montoEfectivo <= 0) || (this.montoDebito <= 0)){

                    // console.log("seleccionaste ambos");
                    alert("Efectivo y debito deben existir o ser mayor a cero")
                    this.visualizar_compra = false;
                    return false
                }

            }

            const data = {
                'carro': this.arregloCarro,
                'venta_total': this.total,
                'forma_pago_id': this.formaPago[0] + ',' + this.formaPago[1],
                'tipo_entrega_id': this.entrega,
                'reseptor': cliente,
                'pago_efectivo': this.montoEfectivo,
                'pago_debito': this.montoDebito,
                'chk_credito': this.chk_credito,
                'detalle_credito': this.detalle_credito,
                'monto_credito': this.monto_credito,
                'sii_forma_pago': this.sii_forma_pago,
                'tipo_venta_id' : 33, //FACTURACION ELECTRONICA
                'fecha' : this.date.date
                // 'vuelto': (Number(this.montoEfectivo)+ Number(this.montoDebito)) - Number(this.total)
            }



            this.axios.post('api/ver_antes_dte_33', data).then((response) => {

                if (response.data.estado == 'success') {

                    this.pre_factura = response.data.factura;
                    console.log(this.pre_factura);
                    this.$refs['modal_factura'].show();

                    this.visualizar_compra = false;

                    //guardamos la ID de la venta en local Storage, por cada venta se ira actualizando
                    // localStorage.setItem('venta_id', this.ticketPrint[0].idVenta);
                    // this.local_storage_venta = localStorage.getItem('venta_id');
                }

                if (response.data.estado == 'failed') {
                    this.showAlert6();
                    this.visualizar_compra = false;
                    this.errores6 = response.data.mensaje;
                }
                if (response.data.estado == 'failed_v') {
                    this.errores3 = response.data.mensaje;
                    this.visualizar_compra = false;
                    return false;
                }

            });



        },

        formatPrice(value) {
            let val = (value / 1).toFixed(0).replace('.', ',')
            return val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".")
        },

        // SUCCESS VENTA CONTADOR
        countDownChanged3(dismissCountDown3) {
            this.dismissCountDown3 = dismissCountDown3;
            if (this.dismissCountDown3 == 0) {
                this.limpiarCarro();
            }
        },
        showAlert3() {
            this.dismissCountDown3 = this.dismissSecs3;
        },

        // SUCCESS CARRO
        countDownChanged4(dismissCountDown4) {
            this.dismissCountDown4 = dismissCountDown4;
        },
        showAlert4() {
            this.dismissCountDown4 = this.dismissSecs4;
        },
        // PRODUCTO REPETIDO
        countDownChanged5(dismissCountDown5) {
            this.dismissCountDown5 = dismissCountDown5;
        },
        showAlert5() {
            this.dismissCountDown5 = this.dismissSecs5;
        },

        // CAMPOS VACIOS O STOCK MAYOR
        countDownChanged6(dismissCountDown6) {
            this.dismissCountDown6 = dismissCountDown6;
        },
        showAlert6() {
            this.dismissCountDown6 = this.dismissSecs6;
        },

        traer_producto() {
            this.listarCarro = [];
            this.axios.get('api/buscar_producto_carro/' + this.buscadorProducto).then((response) => {

                if (this.buscadorProducto == '') {
                    this.showAlertBuscar = true;
                    this.errorBuscar = ("El campo no puede quedar vacio, ingrese un producto porfavor.");
                } else {

                    if (response.data.estado == 'success') {
                        this.listarCarro = response.data.producto[0];
                        this.agregar(this.buscadorProducto);
                        this.buscadorProducto = '';
                        this.buscando_txt = '';
                        this.lista_buscando = [];
                        this.view_buscando = false;
                        console.log(this.view_buscando);
                        this.btn_buscar_producto = true;
                    } else {

                        if (response.data.estado == 'failed') {
                            this.showAlert4();
                            this.errores4 = response.data.mensaje;
                            console.log(this.errores4);
                            this.buscadorProducto = '';
                            this.btn_buscar_producto = true;
                        }
                    }
                }

            })
                .catch(error => {
                    alert(error);
                })
        },

        agregar(sku) {
            let existe = false;
            for (let i = 0; i < this.arregloCarro.length; i++) {
                if (this.listarCarro.sku == this.arregloCarro[i].sku) {
                    existe = true;
                    break;
                }
            }
            if (existe == true) {
                this.listarCarro = [];
               console.log("inout");

                this.arregloCarro.map(function(item, index) {

                    if(item.sku == sku){
                        const input = document.getElementsByName("input_cantidad");
                        const input_posicion = input[index];
                        item.cantidad_ls = Number(item.cantidad_ls) + 1;
                        input_posicion.value = item.cantidad_ls;
                        console.log('item: ',index, item.cantidad_ls)

                           input_posicion.click();
                        //    console.log(this.ingresar_cantidad_carro(index, item.cantidad_ls));
                    }
                });


                this.showAlert5();
                // console.table(this.arregloCarro)
            } else {
                this.arregloCarro.push(this.listarCarro);
                localStorage.setItem('Carro', JSON.stringify(this.arregloCarro));
                this.total_temporal();
            }
        },

        cargarCarro() {
            if (localStorage.getItem("Carro") != null) {
                let carroGuardado = JSON.parse(localStorage.getItem("Carro"));
                this.total = 0;
                this.totalTemporal = 0;
                for (let i = 0; i < carroGuardado.length; i++) {
                    this.arregloCarro.push(carroGuardado[i]);
                    this.totalTemporal = parseInt(this.arregloCarro[i].precio) * (this.arregloCarro[i].cantidad_ls);
                    this.total = parseInt(this.total + this.totalTemporal);
                }
            }

        },

        ingresar_cantidad_carro(index, valor) {

            //  console.log($event.target.value);
            this.arregloCarro[index].cantidad_ls = valor;
            console.log('carga:',this.arregloCarro[index].cantidad_ls);

            this.tipos_imp_adicionales.map( loop =>{
                console.log("loop: ",loop.decimal)
                if(this.arregloCarro[index].tipo_impuesto_adicional == loop.id){

                    if(loop.id == 35 || loop.id == 28){
                       console.log("Monto manual")
                    }else{
                        console.log("soy los calculables");
                        const monto_neto = (this.arregloCarro[index].precio * this.arregloCarro[index].cantidad_ls) - Math.round((parseInt(this.arregloCarro[index].precio) * (this.arregloCarro[index].cantidad_ls)) * ((this.arregloCarro[index].descuento)?this.arregloCarro[index].descuento:0) /100);
                        this.arregloCarro[index].monto_impuesto_adicional = Math.round(monto_neto * (1 + loop.decimal)) - monto_neto
                    }

                }
            });


            this.total_temporal();
            this.total_temporal_monto_especifico();
            localStorage.removeItem('Carro');
            localStorage.setItem("Carro", JSON.stringify(this.arregloCarro));

        },
        ingresar_unidad_carro(index, valor) {

            //  console.log($event.target.value);
            this.arregloCarro[index].unidad = valor;
            console.log(this.arregloCarro[index].unidad);
            this.total_temporal();
            localStorage.removeItem('Carro');
            localStorage.setItem("Carro", JSON.stringify(this.arregloCarro));
            console.log(localStorage.getItem("Carro"));

        },
        ingresar_descuento_carro(index, valor) {

            //  console.log($event.target.value);
            this.arregloCarro[index].descuento = valor;
            const item_monto_neto = (this.arregloCarro[index].precio * this.arregloCarro[index].cantidad_ls) - Math.round((parseInt(this.arregloCarro[index].precio) * (this.arregloCarro[index].cantidad_ls)) * ((this.arregloCarro[index].descuento)?this.arregloCarro[index].descuento:0) /100);
            this.arregloCarro[index].item_neto = (this.arregloCarro[index].precio * this.arregloCarro[index].cantidad_ls);
            this.arregloCarro[index].item_descontado = item_monto_neto;
            this.arregloCarro[index].monto_descuento = this.arregloCarro[index].item_neto - this.arregloCarro[index].item_descontado
            console.log(this.arregloCarro[index].descuento);

            if(this.arregloCarro[index].afecto == "true"){
                console.log("me entrooo en descuentos");
                this.tipos_imp_adicionales.map( loop =>{
                    console.log("loop: ",loop.decimal)
                    if(this.arregloCarro[index].tipo_impuesto_adicional == loop.id){

                        if(loop.id == 35 || loop.id == 28){
                            console.log("PONE VO EL MONTO LOJI")
                         }else{
                             console.log("soy los calculables");
                             const monto_neto = (this.arregloCarro[index].precio * this.arregloCarro[index].cantidad_ls) - Math.round((parseInt(this.arregloCarro[index].precio) * (this.arregloCarro[index].cantidad_ls)) * ((this.arregloCarro[index].descuento)?this.arregloCarro[index].descuento:0) /100);
                            //  this.arregloCarro[index].monto_impuesto_adicional = Math.round(monto_neto * (1 + loop.decimal)) - monto_neto
                         }
                    }
                });
            }


            this.total_temporal();
            this.total_temporal_monto_especifico();
            localStorage.removeItem('Carro');
            localStorage.setItem("Carro", JSON.stringify(this.arregloCarro));
            console.log(localStorage.getItem("Carro"));

        },

        ingresar_afecto_carro(index, valor){


            this.arregloCarro[index].afecto = valor;
            //si no es afecto a IVA entonces, desactivar el tipo impuesto adicional con el monto de este mismo
            // if(valor == "false"){

            //     this.arregloCarro[index].tipo_impuesto_adicional = '';
            //     this.arregloCarro[index].activo_iva = true;
            //     // document.getElementsByName('input_tipo_impuesto_adicional')[index].disabled = true;
            //     // document.getElementsByName('input_tipo_impuesto_adicional')[index].value = '';
            //     // document.getElementsByName('input_monto_impuesto_adicional')[index].disabled = true;
            //     // document.getElementsByName('input_monto_impuesto_adicional')[index].value = '0';
            //     // this.arregloCarro[index].monto_impuesto_adicional = null;

            // }else{
            //     this.arregloCarro[index].activo_iva = false;
            //     // document.getElementsByName('input_tipo_impuesto_adicional')[index].disabled = false;
            //     // document.getElementsByName('input_monto_impuesto_adicional')[index].disabled = false;
            // }

            this.total_temporal();
            this.total_temporal_monto_especifico();
            localStorage.removeItem('Carro');
            localStorage.setItem("Carro", JSON.stringify(this.arregloCarro));
            // console.log(localStorage.getItem("Carro"));
        },

        ingresar_tipo_imp_adic_carro(index, valor){
            this.arregloCarro[index].tipo_impuesto_adicional = valor;


            //si el imp adicional es de gacolina o diesel, especificar manualmente el monto
            if(valor == 35 || valor == 28){
                alert("especidique el impuesto adicional")


            }else{ //o si no, calcular segun valor iva
                this.tipos_imp_adicionales.map( loop =>{
                    console.log("loop: ",loop.decimal)
                    if(valor == loop.id){
                        alert(loop.decimal);
                        const monto_neto = (this.arregloCarro[index].precio * this.arregloCarro[index].cantidad_ls) - Math.round((parseInt(this.arregloCarro[index].precio) * (this.arregloCarro[index].cantidad_ls)) * ((this.arregloCarro[index].descuento)?this.arregloCarro[index].descuento:0) /100);
                        this.arregloCarro[index].monto_impuesto_adicional = Math.round(monto_neto * (1 + loop.decimal)) - monto_neto;

                    }
                });
            }

            this.total_temporal();
            this.total_temporal_monto_especifico();
            localStorage.removeItem('Carro');
            localStorage.setItem("Carro", JSON.stringify(this.arregloCarro));
            // console.log(localStorage.getItem("Carro"));
        },

        ingresar_mia_carro(index, valor){
            this.arregloCarro[index].monto_impuesto_adicional = valor;
            this.total_temporal();
            this.total_temporal_monto_especifico();
            localStorage.removeItem('Carro');
            localStorage.setItem("Carro", JSON.stringify(this.arregloCarro));
            console.log(localStorage.getItem("Carro"));
        },
        total_temporal_monto_especifico(){
            this.impuesto_especifico=0;
            const vue = this;

            this.arregloCarro.map(function(item, index) {

                vue.impuesto_especifico = Number(vue.impuesto_especifico) + Number(item.monto_impuesto_adicional)

            });
        },

        eliminarItem(indice) {
            this.arregloCarro.splice(indice, 1);
            this.total_temporal();
            this.total_temporal_monto_especifico();
            localStorage.removeItem('Carro');
            localStorage.setItem("Carro", JSON.stringify(this.arregloCarro));

        },

        limpiarCarro() {
            localStorage.removeItem('Carro');
            this.arregloCarro = [];
            this.total = 0;
            this.impuesto_especifico = 0;
            this.suma_solo_ivas = 0;
            this.suma_solo_exento = 0;
            this.montoDebito = 0;
            this.montoCredito = 0;
            this.montoEfectivoDebito = 0;
            this.efectivo = false;
            this.debito = false;
            this.credito = false;
            this.formaPago = [];
            this.entrega = [];
        },

        total_temporal() {
            this.total = 0;
            this.totalTemporal = 0;
            this.suma_solo_ivas =0;
            this.suma_solo_exento = 0;
            for (let i = 0; i < this.arregloCarro.length; i++) {
                 //   sub total - ((subtotal) * descuento / 100)
                console.log(parseInt(this.arregloCarro[i].precio) * (this.arregloCarro[i].cantidad_ls)+' - '+((parseInt(this.arregloCarro[i].precio) * (this.arregloCarro[i].cantidad_ls)) * ((this.arregloCarro[i].descuento)?this.arregloCarro[i].descuento:0) /100))
                this.totalTemporal = parseInt(this.arregloCarro[i].precio) * (this.arregloCarro[i].cantidad_ls) - Math.round((parseInt(this.arregloCarro[i].precio) * (this.arregloCarro[i].cantidad_ls)) * ((this.arregloCarro[i].descuento)?this.arregloCarro[i].descuento:0) /100)  ;
                console.log("total temporal: "+this.totalTemporal);

                this.total = parseInt(this.total + this.totalTemporal);
                console.log("total: "+this.total);

                //  console.log($event.target.value);
                // this.arregloCarro[i].descuento = valor;
                const item_monto_neto = (this.arregloCarro[i].precio * this.arregloCarro[i].cantidad_ls) - Math.round((parseInt(this.arregloCarro[i].precio) * (this.arregloCarro[i].cantidad_ls)) * ((this.arregloCarro[i].descuento)?this.arregloCarro[i].descuento:0) /100);
                this.arregloCarro[i].item_neto = (this.arregloCarro[i].precio * this.arregloCarro[i].cantidad_ls);
                this.arregloCarro[i].item_descontado = item_monto_neto;
                this.arregloCarro[i].monto_descuento = this.arregloCarro[i].item_neto - this.arregloCarro[i].item_descontado
                console.log(this.arregloCarro[i].descuento);

                //recalculo de solo afectos a iva
                console.log("veni afecto: "+this.arregloCarro[i].afecto+' . '+this.arregloCarro[i].nombre);
                if(this.arregloCarro[i].afecto == 'true'){
                    this.suma_solo_ivas = this.suma_solo_ivas + parseInt(this.arregloCarro[i].precio) * (this.arregloCarro[i].cantidad_ls) - Math.round((parseInt(this.arregloCarro[i].precio) * (this.arregloCarro[i].cantidad_ls)) * ((this.arregloCarro[i].descuento)?this.arregloCarro[i].descuento:0) /100);

                    console.log('solo_iva: '+this.suma_solo_ivas)
                }
                if(this.arregloCarro[i].afecto == 'false'){
                    this.suma_solo_exento = this.suma_solo_exento + parseInt(this.arregloCarro[i].precio) * (this.arregloCarro[i].cantidad_ls) - Math.round((parseInt(this.arregloCarro[i].precio) * (this.arregloCarro[i].cantidad_ls)) * ((this.arregloCarro[i].descuento)?this.arregloCarro[i].descuento:0) /100);
                }
            }

        },
        url(ruta) {
            this.$router.push({ path: ruta }).catch(error => {
              if (error.name != "NavigationDuplicated") {
                throw error;
              }
            });
          },
        emitir_dte33(factura, neto, exento, imp_especifico, iva, bruto, vuelto, deuda, credito ){
            // console.log(factura, neto, imp_especifico, iva, bruto, vuelto, credito);


            const data = {
                factura: factura,
                totales:{
                    Neto: neto,
                    Exento: exento,
                    Iva: iva,
                    Especifico: imp_especifico,
                    Total: Math.round(bruto)
                },
                total: Math.round(neto),// total neto solo para vildar que no vaiga en cero al back
                vuelto: vuelto,
                deuda: deuda,
                credito: credito,
                sii_forma_pago: this.sii_forma_pago,
                forma_pago: this.formaPago,
                forma_pago_id: this.formaPago[0] + ',' + this.formaPago[1],
                efectivo: this.montoEfectivo,
                debito:this.montoDebito,
                detalle_credito: this.detalle_credito,
                cliente_id: this.cliente_id,
                dte_precio: this.dte_precio,
                tipo_venta_id:'33'// facturacion electronica;

            };


            this.axios.post('api/emitir_dte_33', data).then((res)=>{
                if(res.data.estado == 'success'){
                    this.limpiarCarro();
                    //guardamos la ID de la venta en local Storage, por cada venta se ira actualizando
                     localStorage.setItem('fac_venta_id', res.data.venta.id);
                     this.local_storage_venta = localStorage.getItem('fac_venta_id');
                    alert("Factura electronica emitida");
                    this.$refs['modal_factura'].hide();
                    document.getElementById('btn_ultima_venta').click()
                    document.getElementById('rut').value = '';
                    this.ver_cliente = false;
                    this.mostrar_cliente = [];

                    // this.url('index');

                }
            });
        },
        dte_34(){
            this.axios.get('api/super_factura').then((res)=>{

            });
        },
        consultar_folios(){
            this.axios.get('api/consulta_folios').then((res)=>{});
        },
        registrar_venta(cliente) {
            this.confirm_compra = true;
            if (document.getElementById('rut').value.trim() == ''){

                alert("Falta el cliente");
                this.confirm_compra = false;
                 return false;
            }

            if(this.chk_credito == true){
                if(this.monto_credito <= 0 || this.monto_credito == '' || this.monto_credito == null){
                    alert("El monto del credito debe ser mayor a 0")
                    this.confirm_compra = false;
                    return false;
                }
                if( this.detalle_credito == '' || this.detalle_credito == null){
                    alert("El detalle del credito no debe estar vacio")
                    this.confirm_compra = false;
                    return false;
                }
            }

            const data = {
                'carro': this.arregloCarro,
                'venta_total': this.total,
                'forma_pago_id': this.formaPago[0] + ',' + this.formaPago[1],
                'tipo_entrega_id': this.entrega,
                'cliente': cliente,
                'pago_efectivo': this.montoEfectivo,
                'pago_debito': this.montoDebito,
                'chk_credito': this.chk_credito,
                'detalle_credito': this.detalle_credito,
                'monto_credito': this.monto_credito,
                //este campo es nuevo
                'tipo_venta_id' : 33 //FACTURACION ELECTRONICA
                // 'vuelto': (Number(this.montoEfectivo)+ Number(this.montoDebito)) - Number(this.total)
            }



            this.axios.post('api/emitir_dte_33', data).then((response) => {

                if (response.data.estado == 'success') {
                    //limpiamos cada vez que se genera una venta el ID venta
                    localStorage.removeItem('fac_venta_id');
                    this.producto_id = '';
                    this.errores3 = [];
                    // this.limpiarCarro();
                    this.correcto3 = response.data.mensaje;
                    this.chk_credito = false;
                    this.monto_credito = 0;
                    this.detalle_credito = '';
                    this.cliente_id = '';
                    this.montoEfectivo = 0;
                    this.montoDebito = 0;
                    this.buscando_txt = '';
                    this.showAlert3();
                    this.showModal();
                    this.ticketPrintDetalle = response.data.ticketDetalle;
                    this.cliente = response.data.cliente;
                    this.ticketPrint = response.data.ticket;
                    this.get_vuelto = response.data.vuelto;

                    this.confirm_compra = false;

                    //guardamos la ID de la venta en local Storage, por cada venta se ira actualizando
                    // localStorage.setItem('venta_id', this.ticketPrint[0].idVenta);
                    // this.local_storage_venta = localStorage.getItem('venta_id');
                }

                if (response.data.estado == 'failed') {
                    this.showAlert6();
                    this.confirm_compra = false;
                    this.errores6 = response.data.mensaje;
                }
                if (response.data.estado == 'failed_v') {
                    this.errores3 = response.data.mensaje;
                    this.confirm_compra = false;
                    return false;
                }

            });
        },

        //ultima venta del comprobante :1
        abrir_ultima_venta(component, venta_id){
            this.traer_ul_venta = true;
            this.axios.get('api/comprobante/'+venta_id).then((res)=>{
                if(res.data.estado == 'success'){
                    this.emisor = res.data.configuraciones.emisor;
                    this.fac_venta = res.data.venta;
                    this.post_factura = res.data.factura;
                    this.fac_cliente = res.data.factura.Cliente;
                    this.ticketPrintDetalle = res.data.venta_detalle;
                    this.texto_monto_bruto = res.data.texto_monto_bruto;

                    this.traer_ul_venta = false;
                    this.$refs[""+component+""].show();
                }else{
                    this.traer_ul_venta = false;
                }
            });
        },
        escribiendoProducto() {
            if (this.buscadorProducto.toLowerCase().trim() == '') {
                this.btn_buscar_producto = true;
            } else {

                this.btn_buscar_producto = false;
            }
        },

        traer_configuraciones() {
            this.axios.get('api/traer_configuraciones').then((response) => {
                if (response.data.estado == 'success') {
                    this.logoNull = true;
                    this.listarConf = response.data.configuraciones;

                }
            })
        },

        getData(sku) {

             this.buscadorProducto = sku;
             this.traer_producto();


        },

        redirect_user($id){
            this.exist = false;
            this.$router.push({name: 'User', params: {id:$id }});
        },







        verifica_existe_periodo(){
            var per = false;
            var caj = false;
            this.axios.get('api/verifica_existe_periodo').then((res)=>{

                this.nombre_caja = res.data.data_caja;
                this.estado_periodo = res.data.periodo.estado;
                this.estado_caja = res.data.caja.estado;

                if(res.data.periodo.estado == 'INACTIVO'){
                    per = true;
                    // this.estado_periodo = res.data.periodo.estado;

                }

                if(res.data.caja.estado == 'INACTIVO'){
                    caj = true;
                    // this.estado_caja = res.data.caja.estado;
                }

                if(per == true || caj == true){ // si alguno viene INACTIVO se abre modal de alerta
                    this.$refs['modal-periodo-caja'].show();
                }
            });
        },

        abrir_periodo_caja(fecha, hora, monto, caja){
            if(fecha == '' || hora == '' || (monto == 0 || monto == '')){
                alert("Faltam campos por llenar, el monto no puede ser tampoco '0'");
                return false;
            }else{
                const data = {
                    fecha_inicio: fecha,
                    hora_inicio: hora,
                    monto_inicio: monto,
                    caja_id: caja
                }

                this.axios.post("api/abrir_periodo_caja", data).then((res) => {
                    if(res.data.estado == 'success'){
                        this.estado_periodo = 'ACTIVO';
                        this.estado_caja = 'ACTIVO';
                        alert(res.data.mensaje);
                        this.$refs['modal-periodo-caja'].hide();
                    }
                    if(res.data.estado == 'failed_inactivo'){
                        this.estado_periodo = 'INACTIVO';
                        this.estado_caja = 'INACTIVO';
                        alert(res.data.mensaje);
                    }
                    if(res.data.estado == 'failed_activo'){
                        this.estado_periodo = 'ACTIVO';
                        this.estado_caja = 'ACTIVO';
                        alert(res.data.mensaje);
                        this.$refs['modal-periodo-caja'].hide();
                    }
                });
            }

        },

        abrir_solo_caja(fecha, hora, monto, caja){
            if(fecha == '' || hora == '' || (monto == 0 || monto == '')){
                alert("Faltam campos por llenar, el monto no puede ser tampoco '0'");
                return false;
            }else{

                this.btn_abrir_caja = true;
                const data = {
                    fecha_inicio: fecha,
                    hora_inicio: hora,
                    monto_inicio: monto,
                    caja_id: caja
                }

                this.axios.post("api/abrir_solo_caja", data).then((res) => {
                    if(res.data.estado == 'success'){
                        // this.estado_periodo = 'ACTIVO';
                        this.estado_caja = 'ACTIVO';
                        this.btn_abrir_caja = false;
                        alert(res.data.mensaje);
                        this.$refs['modal-periodo-caja'].hide();
                        return false;
                    }
                    if(res.data.estado == 'failed_periodo'){
                        this.estado_periodo = 'INACTIVO';
                        alert(res.data.mensaje);
                        this.btn_abrir_caja = false;
                        this.$refs['modal-periodo-caja'].hide();
                        return false;
                    }
                    else{
                        // this.estado_periodo = res.data.activo;
                        this.estado_caja = res.data.activo;
                        alert(res.data.mensaje);
                        this.btn_abrir_caja = false;
                        return false;
                    }
                });
            }
        },

        cargar_datos_caja(caja_id){
            // console.table(caja_id);
            this.axios.get("api/cargar_datos_caja_y_o_periodo/"+caja_id).then((res)=>{
                if(res.data.estado=='success'){
                    this.data_caja_periodo = res.data.datos;
                    this.captura_monto_cierre(this.data_caja_periodo);
                }else{
                    this.data_caja_periodo = res.data.datos; // []
                }
            });
        },

        cargar_datos_periodo(){
            this.axios.get('api/cargar_datos_periodo').then((res)=>{
                if(res.data.estado == 'success'){
                    this.get_datos_periodo = res.data.almacenado_periodo;
                }else{
                    this.get_datos_periodo = [];
                    alert(res.data.mensaje);
                }
            });
        },
        captura_monto_cierre(rcv){
            console.log(rcv.id);
            this.axios.get('api/captura_monto_cierre/'+rcv.id).then((res)=>{
                    this.capta_monto = res.data;
            });
        },

        cerrar_solo_caja(monto_cierre, caja, rcv){
            this.btn_cerrar_caja=true;
            this.axios.post('api/cerrar_solo_caja',{'monto_cierre':monto_cierre, 'caja':caja, 'rcv_id':rcv.id}
            ).then((res) => {
                if(res.data.estado == 'success'){
                    this.estado_caja = 'INACTIVO';
                    this.btn_cerrar_caja=false;
                    alert(res.data.mensaje);
                    this.$refs['modal-cierre-caja-periodo'].hide();

                }else{
                    this.btn_cerrar_caja=false;
                    alert(res.data.mensaje);
                }

            });

        },

        cerrar_periodo(id, estado_caja){
            this.btn_cerrar_periodo = true;

            this.axios.get('api/cerrar_periodo/'+id+'/'+estado_caja).then((res)=>{
                if(res.data.estado == 'success'){
                    this.btn_cerrar_periodo = false;
                    this.estado_periodo = 'INACTIVO';
                    this.$refs['modal-cierre-periodo'].hide();

                    alert(res.data.mensaje); return false;
                }
                else{
                    this.btn_cerrar_periodo = false;
                    this.estado_periodo = 'ACTIVO';
                    alert(res.data.mensaje); return false;
                }
            });
        },





        generar_un_xml(){
            const esto = this;
            alert("entrando..");
            this.axios.get('api/generar_un_xml').then((res)=>{


                this.ted = res.data.xml;
                console.log(this.ted);
            });
        },

        formatear_rut(){
            const $rut = document.getElementById('rut').value
            switch ($rut.length) {
                case 9: //xx.xxx.xxx-x
                    console.log("estan xx.xxx.xxx-x")

                    document.getElementById('rut').value = $rut.replace( /^(\d{2})(\d{3})(\d{3})(\w{1})$/, '$1.$2.$3-$4');

                break;
                case 8: //x.xxx.xxx-x
                    console.log("estan x.xxx.xxx-x");

                    document.getElementById('rut').value = $rut.replace( /^(\d{1})(\d{3})(\d{3})(\w{1})$/, '$1.$2.$3-$4');

                break;

                default:
                    break;
            }
            // console.log(this.rut.length == 8);
            // if(this.rut.length == 8){
            //     console.log("cantidad 9")
            //     this.rut = this.rut.replace( /^(\d{2})(\d{3})(\d{3})(\w{1})$/, '$1.$2.$3-$4')
            // }
            // if(this.rut.length == 7){
            //     console.log("cantidad 8")
            //     this.rut = this.rut.replace( /^(\d{1})(\d{3})(\d{3})(\w{1})$/, '$1.$2.$3-$4')
            // }


        },

        traer_rut_empresa(){
            this.ver_cliente =false;
            this.btn_buscar_rut = true;
            var rut = document.getElementById('rut').value;
            this.axios.get('api/listar_cliente/'+rut).then((res)=>{
                if(res.data.estado == 'success'){
                    this.mostrar_cliente = res.data.cuerpo;
                    this.cliente_id = res.data.cuerpo.id
                    this.ver_cliente = true;
                    this.btn_buscar_rut = false;

                }else{
                    this.mostrar_cliente = [];
                    this.ver_cliente = false;
                    this.btn_buscar_rut = false;
                }
            });
        },




    },

    mounted() {
        this.verifica_existe_periodo();
        this.fecha_hora_acual();
        this.traer_clientes();
        this.cargarCarro();
        this.traer_configuraciones();

        document.getElementById("inputBuscar").focus();

        this.total_temporal();
        this.total_temporal_monto_especifico();


        // this.total_a_pagar = document.getElementById('total_a_pagar').value;

    },
}
