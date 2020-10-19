  
<template>
	<div>
		<center>sitio no encontrado!!</center>
	</div>
</template>

<script>
export default {
	data(){
		return{

		}
	},
	created(){
		if(this.$auth.check()){
			// logeado
			this.$router.push({ name: 'Auth' }).catch(error => {
				if (error.name != "NavigationDuplicated") {
				throw error;
				}
			});
		}else{
			this.$router.push({ name: 'Admin' }).catch(error => {
				if (error.name != "NavigationDuplicated") {
				throw error;
				}
			});
		}
	}
}
</script>