<template>

    <div v-if="usuario.rol==admin">
        <div class="container-fluid mt-4">

             <b-card class="tituloTabla transparencia mb-4">
                <b-card-header class="fondoProductoAdm">Datos para CAF</b-card-header>

                <b-container class="fondoTotal  col-8">
                    <br>
                    <label for="">Llave:</label>
                    <input v-model="llave" type="text" class="form-control" placeholder="Llave acceso api factura...">
                    <br>
                    <label for="">Texto plano XML CAF:</label>
                    <textarea v-model="xml" placeholder="Texto plano del documento CAF...." class="form-control" style="resize:none" name="" id="" cols="30" rows="20"></textarea>
                    <br>
                <button class="btn btn-success" @click="ingresar_caf">Ingresar CAF</button>
                </b-container>

             </b-card>
        </div>
    </div>

</template>

<script>
export default {
    data(){
        return{
            usuario: this.$auth.user(),
            admin:1,

            llave:'',
            xml:'',
        }
    },
    methods:{
        ingresar_caf(){
            const data = {
                llave: this.llave,
                xml: this.xml
            };

            this.axios.post('api/ingresar_caf', data).then((res)=>{

            });
        }
    }
}
</script>

<style>

</style>
