<template>
  <div>
    <div class="row my-4 mx-4">
      <div class="col-12 col-sm-6 col-md-6 col-lg-6 col-xl-3">
        <div class="transparencia">
          <b-card no-body align="center" class="my-2">
            <b-row no-gutters>
              <div class="col-md-6 fondoProductos">
                <img src="images/user.png" class="my-4" width="50px" height="50px" />
              </div>
              <div class="col-md-6 text-center">
                <b-card-text class="my-4">
                  <h4>
                    Usuarios
                    <p class="mt-2">
                      <b>4</b>
                    </p>
                  </h4>
                </b-card-text>
              </div>
            </b-row>
          </b-card>
        </div>
      </div>

      <div class="col-12 col-sm-6 col-md-6 col-lg-6 col-xl-3">
        <div class="transparencia">
          <b-card
            no-body
            align="center"
            class="my-2"
            @click="url('categorias')"
            style="cursor: pointer"
          >
            <b-row no-gutters>
              <div class="col-md-6 fondoProductos">
                <img src="images/list.png" class="my-4" width="50px" height="50px" />
              </div>
              <div class="col-md-6 text-center">
                <b-card-text class="my-4">
                  <h4>
                    Categorias
                    <p class="mt-2">
                      <b>{{cantidadCategorias}}</b>
                    </p>
                  </h4>
                </b-card-text>
              </div>
            </b-row>
          </b-card>
        </div>
      </div>

      <div class="col-12 col-sm-6 col-md-6 col-lg-6 col-xl-3">
        <div class="transparencia">
          <b-card
            no-body
            align="center"
            class="my-2"
            @click="url('administrarProducto')"
            style="cursor: pointer"
          >
            <b-row no-gutters>
              <div class="col-md-6 fondoProductos">
                <img src="images/shopping-cart.png" class="my-4" width="50px" height="50px" />
              </div>
              <div class="col-md-6 text-center">
                <b-card-text class="my-4">
                  <h4>
                    Productos
                    <p class="mt-2">
                      <b>{{cantidadProductos}}</b>
                    </p>
                  </h4>
                </b-card-text>
              </div>
            </b-row>
          </b-card>
        </div>
      </div>

      <div class="col-12 col-sm-6 col-md-6 col-lg-6 col-xl-3">
        <div class="transparencia">
          <b-card
            no-body
            align="center"
            class="my-2"
            @click="url('ventas')"
            style="cursor: pointer"
          >
            <b-row no-gutters>
              <div class="col-md-6 fondoProductos">
                <img src="images/dollar-symbol.png" class="my-4" width="50px" height="50px" />
              </div>
              <div class="col-md-6 text-center">
                <b-card-text class="my-4">
                  <h4>
                    Ventas
                    <p class="mt-2">
                      <b>
                        <span class="green">$</span>
                        {{ formatPrice(totalVentas) }}
                      </b>
                    </p>
                  </h4>
                </b-card-text>
              </div>
            </b-row>
          </b-card>
        </div>
      </div>
    </div>

    <div class="row m-4">
      <div class="col-12 col-md-12 col-lg-6">
        <b-card class="text-center tituloTabla my-2 col-12 transparencia">
          <b-card-header class="fondoProductos mb-4">PRODUCTOS MAS VENDIDOS</b-card-header>
          <div>
            <b-table
              striped
              hover
              small
              :fields="productosFields"
              :items="productosItems"
              head-variant="dark"
              responsive="sm"
            >
              <!-- A virtual column -->
              <template v-slot:cell(index)="data">{{ data.item.producto_id}}</template>

              <!-- A custom formatted column -->
              <template v-slot:cell(producto)="data">
                <b>{{ data.item.nombre.toUpperCase() }}</b>
              </template>

              <!-- A virtual composite column -->
              <template v-slot:cell(cantidad)="data">
                {{ formatPrice(data.item.cantidad_total) }}
                </template>

              <template v-slot:cell(totalVendido)="data">
                <span class="green">$</span>
                {{ formatPrice(data.item.venta_total) }}
                </template>
            </b-table>
          </div>
        </b-card>
      </div>

      <div class="col-12 col-md-12 col-lg-6">
        <b-card class="text-center tituloTabla my-2 col-12 transparencia">
          <b-card-header class="fondoProductos mb-4">ULTIMAS VENTAS</b-card-header>
          <div>
            <b-table
              striped
              hover
              small
              :fields="ventasFields"
              :items="ventasItems"
              head-variant="dark"
              responsive="sm"
            >
              <template v-slot:cell(index)="data">{{ data.item.id }}</template>

              <template v-slot:cell(producto)="data">
                <b>{{ data.item.nombre.toUpperCase() }}</b>
              </template>

              <template v-slot:cell(fecha)="data">
                {{ data.item.fechaVenta }}
                <br />
                {{data.item.horaVenta}} hrs.
              </template>

              <template v-slot:cell(totalVendido)="data">
                <span class="green">$</span>
                {{ formatPrice(data.item.precio_venta) }}
              </template>
            </b-table>
          </div>
        </b-card>
      </div>
    </div>
  </div>
</template>

<script src="../inicio/inicio.js"></script>
<style src="../inicio/inicio.css"></style>

