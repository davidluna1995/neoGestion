export default {

    data() {

        return {

        }

    },

    methods: {

        url(ruta) {
            this.$router.push({ path: ruta }).catch(error => {
                if (error.name != "NavigationDuplicated") {
                    throw error;
                }
            });
        },

        showModal() {
            this.$refs['editarModal'].show()
        },
        hideModal() {
            this.$refs['editarModal'].hide()
        },

    }
}



