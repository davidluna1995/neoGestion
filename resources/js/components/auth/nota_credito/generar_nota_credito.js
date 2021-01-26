import json_tipos_imp_ad from '../generarVenta/dte_33/tipos_impuestos_adicionales.json'
export default{

    data(){
        return{
            usuario: this.$auth.user(),
            admin:1,

            //datos de referencia al documento boleta o factura
            folio:'',
            documento:'',
            emision:'',
            razon:'',
            //fin datos de referencia

            //tabla
            tabla:{},
            //fin tabla

            //datos_de_factura
            datos:{
                // Cliente:{
                //     rut:''
                // }
            },
            show_datos:false,
            ver_cliente:true,
            arregloCarro:[],
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

            tipos_imp_adicionales:json_tipos_imp_ad,
            dte_precio:'iva_incluido',
            sii_forma_pago: "",
            formaPago: [],
            formaPagoOpcion: [
                { text: 'Efectivo', value: '1' },
                { text: 'T.Debito', value: '2' },
                // { text: 'T.Credito', value: '3' },
            ],
            montoEfectivo: 0,
            montoDebito: 0,
            redon_medio_pago:'DEBITO', //para obtener monto real
            dte_precio:'iva_incluido',
            date:{},
            suma_solo_ivas:0, //afectos
            suma_solo_exento:0, //exentos
            impuesto_especifico:'0',
            total: 0,
            totalTemporal: 0,
            visualizar_nc:false,
            desactivar:false,
            pre_nc:{},
            ref_fecha:'',
        }
    },
    methods:{
        fecha_hora_acual(){
            this.axios.get('api/fecha_hora_actual').then((res)=>{
                this.date = res.data
            });
        },
        venta_por_referencia_nc(){
            const data = {
                folio:this.folio,
                documento:this.documento,
                emision:this.emision,
                razon:this.razon
            };
            this.tabla={};

            this.axios.post('api/venta_por_referencia_nc', data).then((res)=>{
                if(res.data.estado=='success'){
                    localStorage.removeItem('nc_Carro');
                    this.tabla = res.data.tabla;
                    console.log(this.tabla)
                    this.$refs["modal_tabla"].show();
                    if(this.razon == 'Anular documento de referencia'){
                        this.desactivar = true;
                    }else{
                        this.desactivar = false;
                    }
                }else{
                    alert("Nada para referenciar")
                    this.show_datos = false;
                    this.datos = {}
                    this.tabla = {};
                }
            });
        },

        traer_referencia(venta){
            this.arregloCarro={};
            localStorage.removeItem('nc_Carro');

            this.axios.get('api/comprobante_ref/'+venta.id).then((res)=>{
                if(res.data.estado=='success'){
                    localStorage.removeItem('nc_Carro');
                    this.ref_fecha = venta.emision_cl
                    this.datos = res.data;
                    this.$refs["modal_tabla"].hide();
                    this.show_datos = true;
                    //llenar carro de localstorage
                    this.arregloCarro = res.data.factura.Productos;
                    // console.log("res => ", this.arregloCarro)
                    this.sii_forma_pago = res.data.factura.FormaPago_str
                    localStorage.setItem('nc_Carro', JSON.stringify(this.arregloCarro));
                    // this.cargarCarro();
                    this.fecha_hora_acual();
                    this.total_temporal();
                    this.total_temporal_monto_especifico();
                }else{
                    this.show_datos = false;
                    this.datos = {}
                }
            });
        },


        //los ingresar
        ingresar_cantidad_carro(index, valor) {

            //  console.log($event.target.value);
            this.arregloCarro[index].Cantidad = valor;
            console.log('carga:',this.arregloCarro[index].Cantidad);

            this.tipos_imp_adicionales.map( loop =>{
                console.log("loop: ",loop.decimal)
                if(this.arregloCarro[index].TipoImpAdicional == loop.id){

                    if(loop.id == 35 || loop.id == 28){
                       console.log("Monto manual")
                    }else{
                        console.log("soy los calculables");
                        const monto_neto = (this.arregloCarro[index].PrecioNeto * this.arregloCarro[index].Cantidad) - Math.round((parseInt(this.arregloCarro[index].PrecioNeto) * (this.arregloCarro[index].Cantidad)) * ((this.arregloCarro[index].descuento)?this.arregloCarro[index].descuento:0) /100);
                        this.arregloCarro[index].MontoImpAdicional = Math.round(monto_neto * (1 + loop.decimal)) - monto_neto
                    }

                }
            });


            this.total_temporal();
            this.total_temporal_monto_especifico();
            localStorage.removeItem('nc_Carro');
            localStorage.setItem("nc_Carro", JSON.stringify(this.arregloCarro));

        },
        ingresar_unidad_carro(index, valor) {

            //  console.log($event.target.value);
            this.arregloCarro[index].UnidadMedida = valor;
            console.log(this.arregloCarro[index].UnidadMedida);
            this.total_temporal();
            localStorage.removeItem('nc_Carro');
            localStorage.setItem("nc_Carro", JSON.stringify(this.arregloCarro));
            console.log(localStorage.getItem("nc_Carro"));

        },
        ingresar_descuento_carro(index, valor) {

            //  console.log($event.target.value);
            this.arregloCarro[index].descuento = valor;
            const item_monto_neto = (this.arregloCarro[index].PrecioNeto * this.arregloCarro[index].Cantidad) - Math.round((parseInt(this.arregloCarro[index].PrecioNeto) * (this.arregloCarro[index].Cantidad)) * ((this.arregloCarro[index].descuento)?this.arregloCarro[index].descuento:0) /100);
            this.arregloCarro[index].item_neto = (this.arregloCarro[index].PrecioNeto * this.arregloCarro[index].Cantidad);
            this.arregloCarro[index].item_descontado = item_monto_neto;
            this.arregloCarro[index].monto_descuento = this.arregloCarro[index].item_neto - this.arregloCarro[index].item_descontado
            console.log(this.arregloCarro[index].descuento);

            if(this.arregloCarro[index].Afecto == "true"){
                console.log("me entrooo en descuentos");
                this.tipos_imp_adicionales.map( loop =>{
                    console.log("loop: ",loop.decimal)
                    if(this.arregloCarro[index].TipoImpAdicional == loop.id){

                        if(loop.id == 35 || loop.id == 28){
                            console.log("PONE VO EL MONTO LOJI")
                         }else{
                             console.log("soy los calculables");
                             const monto_neto = (this.arregloCarro[index].PrecioNeto * this.arregloCarro[index].Cantidad) - Math.round((parseInt(this.arregloCarro[index].PrecioNeto) * (this.arregloCarro[index].Cantidad)) * ((this.arregloCarro[index].descuento)?this.arregloCarro[index].descuento:0) /100);
                            //  this.arregloCarro[index].monto_impuesto_adicional = Math.round(monto_neto * (1 + loop.decimal)) - monto_neto
                         }
                    }
                });
            }


            this.total_temporal();
            this.total_temporal_monto_especifico();
            localStorage.removeItem('nc_Carro');
            localStorage.setItem("nc_Carro", JSON.stringify(this.arregloCarro));
            console.log(localStorage.getItem("nc_Carro"));

        },

        ingresar_afecto_carro(index, valor){


            this.arregloCarro[index].Afecto = valor;
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
            localStorage.removeItem('nc_Carro');
            localStorage.setItem("nc_Carro", JSON.stringify(this.arregloCarro));
            // console.log(localStorage.getItem("Carro"));
        },

        ingresar_tipo_imp_adic_carro(index, valor){
            this.arregloCarro[index].TipoImpAdicional = valor;


            //si el imp adicional es de gacolina o diesel, especificar manualmente el monto
            if(valor == 35 || valor == 28){
                alert("especidique el impuesto adicional")


            }else{ //o si no, calcular segun valor iva
                this.tipos_imp_adicionales.map( loop =>{
                    console.log("loop: ",loop.decimal)
                    if(valor == loop.id){
                        alert(loop.decimal);
                        const monto_neto = (this.arregloCarro[index].PrecioNeto * this.arregloCarro[index].Cantidad) - Math.round((parseInt(this.arregloCarro[index].PrecioNeto) * (this.arregloCarro[index].Cantidad)) * ((this.arregloCarro[index].descuento)?this.arregloCarro[index].descuento:0) /100);
                        this.arregloCarro[index].MontoImpAdicional = Math.round(monto_neto * (1 + loop.decimal)) - monto_neto;

                    }
                });
            }

            this.total_temporal();
            this.total_temporal_monto_especifico();
            localStorage.removeItem('nc_Carro');
            localStorage.setItem("nc_Carro", JSON.stringify(this.arregloCarro));
            // console.log(localStorage.getItem("Carro"));
        },

        ingresar_mia_carro(index, valor){
            this.arregloCarro[index].monto_impuesto_adicional = valor;
            this.total_temporal();
            this.total_temporal_monto_especifico();
            localStorage.removeItem('Carro');
            localStorage.setItem("nc_Carro", JSON.stringify(this.arregloCarro));
            console.log(localStorage.getItem("nc_Carro"));
        },
        total_temporal_monto_especifico(){
            this.impuesto_especifico=0;
            const vue = this;
            console.log("conxetumadre")
            this.arregloCarro.map(function(item, index) {

                vue.impuesto_especifico = Number(vue.impuesto_especifico) + Number(item.MontoImpAdicional)

            });
        },
        total_temporal() {
            this.total = 0;
            this.totalTemporal = 0;
            this.suma_solo_ivas =0;
            this.suma_solo_exento = 0;
             console.log("mister satan kk", this.arregloCarro.length)
            for (let i = 0; i < this.arregloCarro.length; i++) {
                 //   sub total - ((subtotal) * descuento / 100)
                console.log(parseInt(this.arregloCarro[i].PrecioNeto) * (this.arregloCarro[i].Cantidad)+' - '+((parseInt(this.arregloCarro[i].PrecioNeto) * (this.arregloCarro[i].Cantidad)) * ((this.arregloCarro[i].descuento)?this.arregloCarro[i].descuento:0) /100))
                this.totalTemporal = parseInt(this.arregloCarro[i].PrecioNeto) * (this.arregloCarro[i].Cantidad) - Math.round((parseInt(this.arregloCarro[i].PrecioNeto) * (this.arregloCarro[i].Cantidad)) * ((this.arregloCarro[i].descuento)?this.arregloCarro[i].descuento:0) /100)  ;
                console.log("total temporal: "+this.totalTemporal);

                this.total = parseInt(this.total + this.totalTemporal);
                console.log("total: "+this.total);

                //  console.log($event.target.value);
                // this.arregloCarro[i].descuento = valor;
                const item_monto_neto = (this.arregloCarro[i].PrecioNeto * this.arregloCarro[i].Cantidad) - Math.round((parseInt(this.arregloCarro[i].PrecioNeto) * (this.arregloCarro[i].Cantidad)) * ((this.arregloCarro[i].descuento)?this.arregloCarro[i].descuento:0) /100);
                this.arregloCarro[i].item_neto = (this.arregloCarro[i].PrecioNeto * this.arregloCarro[i].Cantidad);
                this.arregloCarro[i].item_descontado = item_monto_neto;
                this.arregloCarro[i].monto_descuento = this.arregloCarro[i].item_neto - this.arregloCarro[i].item_descontado
                console.log(this.arregloCarro[i].descuento);

                //recalculo de solo afectos a iva

                if(this.arregloCarro[i].Afecto == 'true'){
                    this.suma_solo_ivas = this.suma_solo_ivas + parseInt(this.arregloCarro[i].PrecioNeto) * (this.arregloCarro[i].Cantidad) - Math.round((parseInt(this.arregloCarro[i].PrecioNeto) * (this.arregloCarro[i].Cantidad)) * ((this.arregloCarro[i].descuento)?this.arregloCarro[i].descuento:0) /100);

                    console.log('solo_iva: '+this.suma_solo_ivas)
                }
                if(this.arregloCarro[i].Afecto == 'false'){
                    this.suma_solo_exento = this.suma_solo_exento + parseInt(this.arregloCarro[i].PrecioNeto) * (this.arregloCarro[i].Cantidad) - Math.round((parseInt(this.arregloCarro[i].PrecioNeto) * (this.arregloCarro[i].Cantidad)) * ((this.arregloCarro[i].descuento)?this.arregloCarro[i].descuento:0) /100);
                }
            }

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

        //fin de los ingresar



        formatPrice(value) {
            let val = (value / 1).toFixed(0).replace('.', ',')
            return val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".")
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
        cargarCarro() {
            if (localStorage.getItem("nc_Carro") != null) {
                let carroGuardado = JSON.parse(localStorage.getItem("nc_Carro"));
                this.total = 0;
                this.totalTemporal = 0;
                for (let i = 0; i < carroGuardado.length; i++) {
                    this.arregloCarro.push(carroGuardado[i]);
                    this.totalTemporal = parseInt(this.arregloCarro[i].precio) * (this.arregloCarro[i].cantidad_ls);
                    this.total = parseInt(this.total + this.totalTemporal);
                }
            }

        },


        visualizar_nota_credito(datas){//metodo para visualizar solo notade credito (factura a anular)

            // if(this.estado_caja=='INACTIVO'){
            //     this.$refs['modal-periodo-caja'].show();
            //     return false;
            // }
            // this.limpia_factura();
            // this.visualizar_compra = true;


            //Validamos que el cliente / empresa se esten renderizando en el div para continuar
            // if(this.ver_cliente == false){
            //     alert("El cliente no se esta buscando");
            //     this.visualizar_compra = false;
            //     return false;
            // }

            // if(this.sii_forma_pago.trim()==''){
            //     alert("Falta forma de pago de la factura");
            //     this.visualizar_compra = false;
            //      return false;
            // }
            // if (document.getElementById('rut').value.trim() == ''){

            //     alert("Falta el cliente");
            //     this.visualizar_compra = false;
            //      return false;
            // }
            // si se selecciono "contado" pero el tipo de pago no se ha seleccionado nada entonces validar
            // if(this.sii_forma_pago.trim() =='CONTADO' && this.formaPago.length == 0){
            //     this.visualizar_compra = false;
            //     alert("No ha seleccionado un tipo de pago")
            //     return false;
            // }
            // console.log("carro", this.arregloCarro.length)
            // if(this.arregloCarro.length == 0){
            //     this.visualizar_compra = false;
            //     alert("El carro esta vacio")
            //     return false;
            // }
            // o si aparte de estar en contado hay algun tipo de pago seleccionado, entonces ver cual esta activo para asi validar
            // if(this.sii_forma_pago.trim() =='CONTADO' && this.formaPago.length == 1){
            //     console.log(this.formaPago);
            //     //si esta seleccionado solo efectivo, entonces
            //     if(this.formaPago[0] == '1'){
            //         // console.log("seleccionaste solo efectivo");
            //         if(this.montoEfectivo =='' || this.montoEfectivo <= 0){

            //             alert("El monto efectivo debe existir o ser mayor a cero")
            //             this.visualizar_compra = false;
            //             return false
            //         }
            //     }

            //     //si esta seleccionado solo efectivo, entonces
            //     if(this.formaPago[0] == '2'){
            //         // console.log("seleccionaste solo debito");
            //         if( this.montoDebito <= 0){
            //             alert("El monto debito debe existir o ser mayor a cero")
            //             this.visualizar_compra = false;
            //             return false
            //         }
            //     }

            // }

            // o si aparte de estar en contado pero "ambos" tipos seleccionado
            // if(this.sii_forma_pago.trim() =='CONTADO' && this.formaPago.length == 2){
            //     //si esta seleccionado  efectivo y debito entonces
            //     if((this.montoEfectivo <= 0) || (this.montoDebito <= 0)){

            //         // console.log("seleccionaste ambos");
            //         alert("Efectivo y debito deben existir o ser mayor a cero")
            //         this.visualizar_compra = false;
            //         return false
            //     }

            // }

            const data = {
                'carro': this.arregloCarro,
                'venta_total': this.total,
                'forma_pago_id': this.formaPago[0] + ',' + this.formaPago[1],
                'tipo_entrega_id': this.entrega,
                'documento': datas, //aqui va todo lo que era de la factura
                'pago_efectivo': this.montoEfectivo,
                'pago_debito': this.montoDebito,
                'chk_credito': this.chk_credito,
                'detalle_credito': this.detalle_credito,
                'monto_credito': this.monto_credito,
                'sii_forma_pago': this.sii_forma_pago,
                'tipo_venta_id' : this.documento, //FACTURACION ELECTRONICA
                'fecha_nota_credito' : this.date.date+' '+this.date.hora,
                'dte_precio': this.dte_precio,
                // 'vuelto': (Number(this.montoEfectivo)+ Number(this.montoDebito)) - Number(this.total)
            }
            console.log(data);
            this.pre_nc = data;
            this.$refs['modal_factura'].show();
            return false;



            this.axios.post('api/ver_antes_dte_33', data).then((response) => {

                if (response.data.estado == 'success') {

                    this.pre_factura = response.data.factura;
                    console.log(this.pre_factura);
                    this.$refs['modal_factura'].show();

                    this.visualizar_nc = false;

                    //guardamos la ID de la venta en local Storage, por cada venta se ira actualizando
                    // localStorage.setItem('venta_id', this.ticketPrint[0].idVenta);
                    // this.local_storage_venta = localStorage.getItem('venta_id');
                }

                if (response.data.estado == 'failed') {
                    this.showAlert6();
                    this.visualizar_nc = false;
                    this.errores6 = response.data.mensaje;
                }
                if (response.data.estado == 'failed_v') {
                    this.errores3 = response.data.mensaje;
                    this.visualizar_nc = false;
                    return false;
                }

            });



        },
    },

}
