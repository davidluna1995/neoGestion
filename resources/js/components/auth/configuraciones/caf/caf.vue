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
                    <input type="file" name="archivo_caf" id="archivo_caf" @change="readText($event)">
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
            txt_caf:'',
        }
    },
    methods:{
        ingresar_caf(){
            const form = new FormData();
            // form.append('archivo_caf', document.getElementsByName('archivo_caf').files.item[0]);
            form.append('xml', this.xml);
            form.append('txt_caf', this.txt_caf);
            // const data = {
            //     // llave: this.llave,
            //     xml: this.xml
            // };

            this.axios.post('api/ingresar_caf', form).then((res)=>{

            });
        },

         readText(event) {
            const archivo = event.srcElement.files[0]
            const file = event.target.files.item(0)
            const text =  file.text();

            const promise = Promise.resolve(text);
            promise.then((value) => {
            this.txt_caf = (value);

            });

        }
    }
}
</script>

<style>

</style>
