<template>
  <div>
    <div class="row justify-content-center m-4">
      <div class="col-12 col-md-12">
        <b-card class="text-center tituloTabla transparencia">
          <b-card-header class="fondoProductoAdm mb-4">LISTA DE PRODUCTOS</b-card-header>

          <div class="row">
            <div class="col-12 col-sm-8 col-md-8 col-lg-8 col-xl-8">
              <b-input-group>
                <b-form-input
                  v-on:keyup="escribiendoProducto"
                  placeholder="Buscar producto"
                  v-model="buscadorProducto"
                ></b-form-input>
              </b-input-group>
            </div>
            <div class="col-12 col-sm-2 col-md-2 col-lg-2 col-xl-2 mx-auto mb-4">
              <b-button
                :disabled="btn_buscar_producto"
                block
                variant="success"
                @click="traer_producto()"
              >
                <i class="fas fa-search text-light"></i>
              </b-button>
            </div>
            <div class="col-12 col-sm-2 col-md-2 col-lg-2 col-xl-2 mx-auto mb-4">
              <b-button block variant="success" @click="traer_ventas()">
                <i class="fas fa-sync-alt text-light"></i>
              </b-button>
            </div>
            <!-- BUSQUEDA ERRONEA -->
            <div class="col-12">
              <ul>
                <!-- SUCCESS BUSQUETA -->
                <b-alert
                  variant="danger"
                  :show="dismissCountDown4"
                  @dismissed="dismissCountDown4=0"
                  @dismiss-count-down="countDownChanged4"
                >
                  <p>{{errores4}} {{ dismissCountDown4 }} segundos...</p>
                  <b-progress
                    variant="danger"
                    height="4px"
                    :max="dismissSecs4"
                    :value="dismissCountDown4"
                  ></b-progress>
                </b-alert>
              </ul>
            </div>
            <!-- BUSCADOR VACIO -->
            <div class="col-12">
              <b-alert v-model="showAlertBuscar" variant="danger" dismissible>{{errorBuscar}}</b-alert>
            </div>
          </div>

          <div>
            <b-table
              id="app"
              table
              hove12
              hovexl-r
              bordered
              small
              :fields="ventasFieldsAdm"
              :items="listarVentas"
              sticky-header="300px"
              head-variant="dark"
              responsive="sm"
            >
              <template v-slot:cell(index)="data">{{ data.item.id }}</template>
              <template v-slot:cell(prod)="data">{{ data.item.nombre }}</template>
              <template v-slot:cell(cat)="data">{{ data.item.catDesc }}</template>
              <template v-slot:cell(desc)="data">{{ data.item.proDesc }}</template>

              <template v-slot:cell(ventaUn)="data">
                <span class="green">$</span>
                {{ formatPrice(data.item.precio_venta) }}
              </template>

              <template v-slot:cell(cant)="data">{{formatPrice(data.item.cantidad)}}</template>

              <template v-slot:cell(venta)="data">
                <span class="green">$</span>
                {{ formatPrice(data.item.venta) }}
              </template>

              <template v-slot:cell(fecha)="data">
                {{ data.item.fechaVenta }}
                <br />
                {{data.item.horaVenta}} hrs.
              </template>
            </b-table>
          </div>
        </b-card>
      </div>
    </div>

      <!-- <button @click="download">Download</button> -->
  </div>
</template>

<script src="../ventas/ventas.js"></script>
<style scoped src="../ventas/ventas.css"></style>
