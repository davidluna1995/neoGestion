import Multiselect from 'vue-multiselect';

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
            cliente_id: null,
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

            // CABEZERA DE LA TABLA AGREGAR
            carroFieldsAdm: [
                { key: 'sku', label: 'SKU' },
                { key: 'cantidad', label: 'Cantidad' },
                { key: 'producto', label: 'Producto' },
                { key: 'precioProd', label: 'Precio Producto' },
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
            dismissSecs3: 10,
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

            // fecha_cierre:'',
            // hora_cierre:'',
            cierre_monto:0,
            btn_cerrar_caja:false,
            btn_abrir_caja:false,
            btn_cerrar_periodo:false,
            get_datos_periodo:[],
        }


    },

    created() {

        console.log(document);
        setInterval(this.getNow, 1000);

    },



    methods: {

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
            console.log('carga:',this.arregloCarro[index].cantidad_ls)
            this.total_temporal();
            localStorage.removeItem('Carro');
            localStorage.setItem("Carro", JSON.stringify(this.arregloCarro));

        },

        eliminarItem(indice) {
            this.arregloCarro.splice(indice, 1);
            this.total_temporal();
            localStorage.removeItem('Carro');
            localStorage.setItem("Carro", JSON.stringify(this.arregloCarro));

        },

        limpiarCarro() {
            localStorage.removeItem('Carro');
            this.arregloCarro = [];
            this.total = 0;
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
            for (let i = 0; i < this.arregloCarro.length; i++) {
                this.totalTemporal = parseInt(this.arregloCarro[i].precio) * (this.arregloCarro[i].cantidad_ls);
                this.total = parseInt(this.total + this.totalTemporal);
            }

        },

        registrar_venta() {

            if(this.estado_caja=='INACTIVO'){
                this.$refs['modal-periodo-caja'].show();
                return false;
            }


            this.confirm_compra = true;
            if (this.cliente_id == null){

                alert("Falta el cliente");
                this.confirm_compra = false;
                 return false;
            }
            const data = {
                'carro': this.arregloCarro,
                'venta_total': this.total,
                'forma_pago_id': this.formaPago[0] + ',' + this.formaPago[1],
                'tipo_entrega_id': this.entrega,
                'cliente_id': this.cliente_id.id,
                'pago_efectivo': this.montoEfectivo,
                'pago_debito': this.montoDebito,
                'chk_credito': this.chk_credito,
                'detalle_credito': this.detalle_credito,
                'monto_credito': this.credito
                // 'vuelto': (Number(this.montoEfectivo)+ Number(this.montoDebito)) - Number(this.total)
            }

            this.axios.post('api/registro_venta', data).then((response) => {
                if (response.data.estado == 'success') {
                    this.producto_id = '';
                    this.errores3 = [];
                    // this.limpiarCarro();
                    this.correcto3 = response.data.mensaje;
                    this.chk_credito = false;
                    this.cliente_id = null;
                    this.montoEfectivo = 0;
                    this.montoDebito = 0;
                    this.showAlert3();
                    this.showModal();
                    this.ticketPrintDetalle = response.data.ticketDetalle;
                    this.cliente = response.data.cliente;
                    this.ticketPrint = response.data.ticket;
                    this.get_vuelto = response.data.vuelto;

                    this.confirm_compra = false;

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


        renderChild(data) {
            return `
            <div style="border: 1px solid black;">
                <div class="row">
                    <div class="col-md-2">
                    <img class="avatar_indexx" src="${data.imagen}" />
                    </div>
                    <div class="col-md-6">
                    <span>${data.nombre}</span>
                    </div>
                </div>
             </div>
            `
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



        printDiv(contenido) {

            // html2canvas(document.querySelector("#pdfFactura")).then(canvas => {
            //     document.body.appendChild(canvas)
            // });
            // var ficha = document.getElementById(contenido);
            // var ventanaImpresion = window.open(' ', 'popUp');
            // ventanaImpresion.document.write(ficha.innerHTML);
            // ventanaImpresion.document.close();
            // ventanaImpresion.print();
            // ventanaImpresion.close();
        }

        // getNow: function () {
        //     const today = new Date();
        //     const date = ('0' + today.getDate()).slice(-2) + '/' + ('0' + (today.getMonth() + 1)).slice(-2) + '/' + today.getFullYear();
        //     const time = today.getHours() + ":" + today.getMinutes() + ":" + today.getSeconds();
        //     this.fechaLocal = date;
        //     this.horaLocal = time;
        // },

    },

    mounted() {
        this.traer_clientes();
        this.cargarCarro();
        this.traer_configuraciones();

        document.getElementById("inputBuscar").focus();

        this.verifica_existe_periodo();

    },
}
