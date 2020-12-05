
export default {

props:['listarConf', 'ticketPrint', 'ticketPrintDetalle'],
data(){
    return{
        valor:'jano',
        tipo_doc:'',
    }
},

template: `


    <div
      class="ticket"
      id="printVenta"
      style="
        font-size: 12px;
        font-family: 'Times New Roman';
      "
    >
      <center>
        <img
          :src="listarConf.logo"

          width="177px"
          height="86px"
        />
      </center>
      <center>
        <p>
          TICKET DE VENTA
          <br />
          {{ listarConf.empresa }}
          <br />
          {{ listarConf.direccion }}
        </p>
      </center>
      <table align="center">
        <thead>
          <!--Fecha Emisión-->
          <tr>
            <th colspan="4">
              Fecha: {{ ticketPrint.fecha }}
            </th>
          </tr>
          <tr>
            <th colspan="4">
              Comprobante de Venta Nº {{ ticketPrint.id }}
            </th>
          </tr>

          <tr
            style="
              border-top: 1px solid black;
              border-collapse: collapse;
            "
          >
            <th
              style="
                border-top: 1px solid black;
                border-collapse: collapse;
              "
            >
              PRODUCTO
            </th>
            <th
              style="
                border-top: 1px solid black;
                border-collapse: collapse;
              "
            >
              CANT
            </th>
            <th
              style="
                border-top: 1px solid black;
                border-collapse: collapse;
              "
            >
              PRECIO
            </th>
          </tr>
        </thead>
        <tbody>
          <!--Producto-->
          <tr
            v-for="t in ticketPrintDetalle"
            :key="t.id"
          >
            <td>
              {{ t.nombre }} ($
              {{ formatPrice(t.precio) }} C/U)
            </td>
            <td>{{ t.cantidad }}</td>
            <td class="text-right">
              {{
                formatPrice(
                  t.precio * t.cantidad
                )
              }}
            </td>
          </tr>
          <!--Producto-->
          <br />
          <!--Totales-->
          <tr>
            <td>
              <b>Total:</b>
              $ {{ formatPrice(ticketPrint.venta_total) }}
            </td>
          </tr>
          <tr>
            <td>
              <div>
                <label>
                  <b>Vuelto:</b>
                  $ {{ formatPrice(ticketPrint.vuelto) }} <label v-if="ticketPrint.vuelto < 0" for=""> Deuda de cliente</label>
                </label>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
      <br />
      <center>
        <p class="centrado">
          <b>Cliente: </b>{{ ticketPrint.cliente }}
        </p>
        <p class="centrado">
          ¡GRACIAS POR SU COMPRA!
        </p>
        <p>NEO-GESTION</p>
        <p>.................</p>
      </center>
    </div>
    <!-- MODAL VENTAS  -->

  <div class="row justify-content-center bordeFooter">
    <div class="col-4">
      <b-button
        class="my-2"
        block
        pill
        variant="info"
        onclick="printJS({
                  printable: 'printVenta',
                  type:'html', })"

        >imprimir ticket</b-button>
        <!-- @click="hideModal()" -->
    </div>
  </div>




`,

methods:{
    formatPrice(value) {
        let val = (value / 1).toFixed(0).replace('.', ',')
        return val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",")
    },
}

}
