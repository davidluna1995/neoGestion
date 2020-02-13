<template>
  <div>
    <div class="row m-4 justify-content-center">
      <div class="col-12 col-md-10">
        <b-card class="tituloTabla transparencia">
          <b-card-header class="fondo mb-4 text-center">AGREGAR PRODUCTO</b-card-header>

          <div class="row">
            <div class="col-12 col-md-12 col-lg-6 col-xl-6 mb-4">
              <b-form-group id="nombre" label="Nombre:">
                <div class="row">
                  <div class="col-1">
                    <i class="fas fa-clipboard-list mr-1 mt-1 fa-2x text-secondary"></i>
                  </div>
                  <div class="col-11">
                    <b-form-input v-model="nombre" placeholder="Nombre del producto"></b-form-input>
                  </div>
                </div>
              </b-form-group>
            </div>

            <div class="col-12 col-md-12 col-lg-6 col-xl-6 mb-4">
              <b-form-group id="descripcion" label="Descripcion:">
                <div class="row">
                  <div class="col-1">
                    <i class="fas fa-file-signature mr-1 mt-1 fa-2x text-secondary"></i>
                  </div>
                  <div class="col-11">
                    <b-form-input v-model="descripcion" placeholder="Descripcion del producto"></b-form-input>
                  </div>
                </div>
              </b-form-group>
            </div>

            <!-- <div class="col-12 col-md-12 col-lg-6 col-xl-6 mb-4">
              <b-form-group id="fechaIngreso" label="Fecha de ingreso:">
                <div class="row">
                  <div class="col-1">
                    <i class="fas fa-calendar-alt mr-1 mt-1 fa-2x colorFecha"></i>
                  </div>
                  <div class="col-11">
                    <b-form-input v-model="fecha" type="date" placeholder="fecha ingreso"></b-form-input>
                  </div>
                </div>
              </b-form-group>
            </div>

            <div class="col-12 col-md-12 col-lg-6 col-xl-6 mb-4">
              <b-form-group id="horaIngreso" label="Hora de ingreso:">
                <div class="row">
                  <div class="col-1">
                    <i class="fas fa-clock mr-1 mt-1 fa-2x colorHora"></i>
                  </div>
                  <div class="col-11">
                    <b-form-input v-model="hora" type="time" placeholder="hora"></b-form-input>
                  </div>
                </div>
              </b-form-group>
            </div> -->

            <div class="col-12 col-md-12 col-lg-6 col-xl-6 mb-4">
              <b-form-group id="categoria" label="Selecione Categoria:">
                <div class="row">
                  <div class="col-1">
                    <i class="fas fa-list-ul mr-1 mt-1 fa-2x colorCategoria"></i>
                  </div>
                  <div class="col-11">
                    <multiselect
                      v-model="categoria_id"
                      :options="listarCategorias"
                      :custom-label="buscadorCategorias"
                      open-direction="bottom"
                      placeholder="Seleccione"
                      select-label
                      selectedLabel="Selecionado"
                      deselectLabel="Quitar">
                      <span slot="noResult">No se encontraron resultados.</span>
                    </multiselect>
                  </div>
                </div>
                <!-- <b-form-select v-model="categoria_id" class="mb-3">
                    <b-form-select-option :value="null" disabled>--Seleccione--</b-form-select-option>
                    <b-form-select-option
                      v-for="i in listarCategorias"
                      :key="i.id"
                      :value="i.id"
                    >{{ i.descripcion }}</b-form-select-option>
                </b-form-select>-->
              </b-form-group>
            </div>

            <div class="col-12 col-md-12 col-lg-6 col-xl-6 mb-4">
              <b-form-group id="cantidad" label="Cantidad del producto:">
                
                  <div class="row">
                    <div class="col-1">
                      <i class="fas fa-sort-numeric-up-alt mr-1 mt-1 fa-2x"></i>
                    </div>
                    <div class="col-11">
                      <b-form-input v-model="cantidad" type="number" placeholder="Cantidad"></b-form-input>
                    </div>
                  </div>
              
              </b-form-group>
            </div>

            <div class="col-12 col-md-12 col-lg-6 col-xl-6 mb-4">
              <b-form-group id="compra" label="Ingrese precio de compra:">
                
                  <div class="row">
                    <div class="col-1">
                      <i class="fas fa-dollar-sign mr-1 mt-1 fa-2x text-primary"></i>
                    </div>
                    <div class="col-11">
                      <b-form-input v-model="precio_compra" type="number" placeholder="Precio compra"></b-form-input>
                    </div>
                  </div>
              
              </b-form-group>
            </div>

            <div class="col-12 col-md-12 col-lg-6 col-xl-6 mb-4">
              <b-form-group id="venta" label="Ingrese precio de venta:">
                
                  <div class="row">
                    <div class="col-1">
                      <i class="fas fa-dollar-sign mr-1 mt-1 fa-2x text-success"></i>
                    </div>
                    <div class="col-11">
                      <b-form-input v-model="precio_venta" type="number" placeholder="Precio venta"></b-form-input>
                    </div>
                  </div>
                
              </b-form-group>
            </div>
          </div>

          <div class="row justify-content-center">
            <div class="col-12 col-md-12 col-lg-3 my-2">
              <b-button block pill variant="success"
               @click="registrar_producto();">
                <b>Guardar</b>
              </b-button>
            </div>
            <div class="col-12 col-md-12 col-lg-3 my-2">
              <b-button block pill variant="info" @click="url('administrarProducto')">
                <b>Ver Productos</b>
              </b-button>
            </div>
          </div>

          <!-- alertas -->
          <div class="col-12 my-4">
            <ul v-for="e in errores" :key="e[0]">
              <b-alert variant="danger" show>
                <li>{{e[0]}}</li>
              </b-alert>
            </ul>
          </div>

          <div class="col-12 my-4">
            <ul>
              <b-alert
                :show="dismissCountDown"
                variant="success"
                @dismissed="dismissCountDown=0"
                @dismiss-count-down="countDownChanged"
              >{{correcto}} {{ dismissCountDown }} segundos...</b-alert>
            </ul>
          </div>
        </b-card>
      </div>
    </div>
  </div>
</template>

<script src="../agregarProducto/agregarProducto.js"></script>
<style src="../agregarProducto/agregarProducto.css"></style>
<style src="vue-multiselect/dist/vue-multiselect.min.css"></style>
