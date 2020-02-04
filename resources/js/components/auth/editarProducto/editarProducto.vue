<template>
  <div>
    <b-button size="sm" id="show-btn" @click="showModal" pill block class="my-2" variant="success">Editar</b-button>
    <template>
      <div>
        <b-modal class="modal-header-editar" id="modal-md" size="md" ref="editarModal" hide-footer centered>
          <template v-slot:modal-title>
            <h5 class="text-center">EDITAR PRODUCTO</h5>
          </template>
          <div class="d-block text-center">
            <div class="row">
              <div class="col-10 mb-4">
                <b-input-group>
                  <i class="fas fa-clipboard-list mr-1 mt-1 fa-2x text-secondary"></i>
                  <b-form-input placeholder="Nombre del producto"></b-form-input>
                </b-input-group>
              </div>
              <div class="col-1">
                <b-button block variant="light">
                  <i class="fas fa-edit text-success"></i>
                </b-button>
              </div>

              <div class="col-10 mb-4">
                <b-input-group>
                  <i class="fas fa-file-signature mr-1 mt-1 fa-2x text-secondary"></i>
                  <b-form-input placeholder="Descripcion"></b-form-input>
                </b-input-group>
              </div>
              <div class="col-1">
                <b-button block variant="light">
                  <i class="fas fa-edit text-success"></i>
                </b-button>
              </div>

              <div class="col-10 mb-4">
                <b-input-group>
                  <i class="fas fa-list-ul mr-1 mt-1 fa-2x colorCategoria"></i>
                  <b-form-select></b-form-select>
                </b-input-group>
              </div>
              <div class="col-1">
                <b-button block variant="light">
                  <i class="fas fa-edit text-success"></i>
                </b-button>
              </div>

              <div class="col-10 mb-4">
                <b-input-group>
                  <i class="fas fa-sort-numeric-up-alt mr-1 mt-1 fa-2x"></i>
                  <b-form-input placeholder="Cantidad"></b-form-input>
                </b-input-group>
              </div>
              <div class="col-1">
                <b-button block variant="light">
                  <i class="fas fa-edit text-success"></i>
                </b-button>
              </div>

              <div class="col-10 mb-4">
                <b-input-group>
                  <i class="fas fa-dollar-sign mr-1 mt-1 fa-2x text-primary"></i>
                  <b-form-input placeholder="Precio compra"></b-form-input>
                </b-input-group>
              </div>
              <div class="col-1">
                <b-button block variant="light">
                  <i class="fas fa-edit text-success"></i>
                </b-button>
              </div>

              <div class="col-10 mb-4">
                <b-input-group>
                  <i class="fas fa-dollar-sign mr-1 mt-1 fa-2x text-success"></i>
                  <b-form-input placeholder="Precio venta"></b-form-input>
                </b-input-group>
              </div>
              <div class="col-1">
                <b-button block variant="light">
                  <i class="fas fa-edit text-success"></i>
                </b-button>
              </div>
            </div>
          </div>
          <div class="row justify-content-center bordeFooter">
            <div class="col-4">
              <b-button class="my-2" block pill variant="info" @click="hideModal">Volver</b-button>
            </div>
          </div>
        </b-modal>
      </div>
    </template>
  </div>
</template>


<script src="../editarProducto/editarProducto.js"></script>
<style src="../editarProducto/editarProducto.css"></style>