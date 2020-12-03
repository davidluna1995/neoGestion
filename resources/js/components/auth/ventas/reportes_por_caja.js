

import XLSX from 'xlsx';

    export default {
            components:{

            },
            data(){
                return{
                    usuario: this.$auth.user(),
                    admin:1,

                    select_caja:'',
                    fecha_d:'',
                    fecha_h:'',
                    hora_d:'00:00',
                    hora_h:'23:59',
                    tabla:[],
                    view_tabla:false,

                    tabla_venta:[],
                    cabeza_venta:[],
                    registro_caja_vendedor:'',
                    v_desde:'',
                    v_hasta:'',
                    v_monto_apertura:0,
                    v_monto_cierre:0,

                    caja:'0',
                    btn_filtrar:false,
                }
            },

            methods:{
                 // MODAL VENTAS
                abrir_modal(ref){
                    this.$refs[""+ref+""].show();
                },
                traer_cajas(){
                    this.axios.get('api/all_cajas').then((res)=>{
                        this.select_caja = res.data.caja;
                    });
                },
                traer_caja_reporte(){
                    this.btn_filtrar = true;
                    if(this.fecha_d=='' || this.fecha_h=='' || this.hora_d=='' || this.hora_h==''){
                        this.btn_filtrar = false;
                        alert('Faltan campos por llenar')
                        return false;
                    }

                    const data = {
                        'fecha_d': this.fecha_d,
                        'fecha_h': this.fecha_h,
                        'hora_d': this.hora_d,
                        'hora_h': this.hora_h,
                        'caja': this.caja
                    }
                    this.axios.post('api/reporte_cajas', data).then((res)=>{
                        if(res.data.estado == 'success'){
                            this.tabla = res.data.tabla;
                            this.view_tabla = true;
                            this.btn_filtrar = false;
                        }else{
                            alert("No existen cajas en el rango de fecha seleccionado.")
                            this.btn_filtrar = false;
                            this.view_tabla = false;
                        }
                    });
                },

                cargar_ventas($t){

                    this.registro_caja_vendedor = $t.registro_caja_vendedor_id;
                    this.v_desde =$t.mi_fecha_inicio;
                    this.v_hasta =$t.mi_fecha_cierre;
                    this.v_monto_apertura = $t.mi_monto_inicio;
                    this.v_monto_cierre = $t.mi_monto_cierre;
                    this.axios.get('api/mis_ventas_id/'+$t.registro_caja_vendedor_id+'/'+$t.mi_monto_inicio).then((res)=>{
                        this.tabla_venta = res.data.tabla;
                        this.cabeza_venta = res.data.cabeza;
                    });
                },
                formatPrice(value) {
                    let val = (value / 1).toFixed(0).replace('.', ',')
                    return val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",")
                },

                exportar_tabla: function($id_tabla){

                    var wb = XLSX.utils.book_new();
                    wb.SheetNames.push("Test sheet1");

                    // var ws_data = [['hola','mundo','como tas']];

                    var ws = XLSX.utils.table_to_sheet(document.getElementById(''+$id_tabla+''))
                    wb.Sheets["Test sheet1"] = ws;


                    // var ws2 = XLSX.utils.table_to_sheet(document.getElementById('printVenta'))
                    // wb.SheetNames.push("Test sheet2");
                    // wb.Sheets["Test sheet2"] = ws2;



                    XLSX.writeFile(wb, `test.xlsx`,{ flag: 'w+' })

                },
            },

            created(){
                this.traer_cajas();
            }
}
