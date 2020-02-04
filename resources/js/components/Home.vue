<template>
  <div>
    <div class="row mt-4 justify-content-center">
      <div class="col-11 col-md-3">
        <b-card
          border-variant="primary"
          header="Iniciar Sesion"
          header-bg-variant="primary"
          header-text-variant="white"
          align="center"
        >
          <b-form @submit="onSubmit" @reset="onReset" v-if="show">
            <b-form-group id="input-group-1" label="Rut:" label-for="input-1">
              <b-form-input
                id="input-1"
                v-model="form.rut"
                type="text"
                required
                placeholder="Ingrese su rut"
              ></b-form-input>
            </b-form-group>

            <b-form-group id="input-group-1" label="Contraseña:" label-for="input-1">
              <b-form-input
                id="input-2"
                v-model="form.password"
                type="text"
                required
                placeholder="******"
              ></b-form-input>
            </b-form-group>

            <b-button type="submit" variant="primary">Ingresar</b-button>
            <b-button type="reset" variant="danger">Registrarse</b-button>
          </b-form>
        </b-card>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  data() {
    return {
      form: {
        rut: "",
        password: ""
      },
      show: true
    };
  },
  methods: {
    onSubmit(evt) {
      evt.preventDefault();
      alert(JSON.stringify(this.form));
    },
    onReset(evt) {
      evt.preventDefault();
      // Reset our form values
      this.form.email = "";
      this.form.name = "";
      this.form.food = null;
      this.form.checked = [];
      // Trick to reset/clear native browser form validation state
      this.show = false;
      this.$nextTick(() => {
        this.show = true;
      });
    }
  }
};
</script>