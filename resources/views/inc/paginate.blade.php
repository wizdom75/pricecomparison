<div class="col-md-12">
        <ul class="justify-content-center pagination">
                <li class="page-item previous" >
                        <a class="page-scroll page-link" v-on:click="getResults(pagination.first_page_url)" ><<</a>
                </li>
                <li class="page-item previous" >
                        <a class="page-scroll page-link" v-on:click="getResults(pagination.prev_page_url)" ><</a>
                </li>

                <li class="page-total">Page @{{pagination.current_page}} of @{{pagination.last_page }}</li>

                <li class="page-item next" >
                        <a class="page-scroll page-link" v-on:click="getResults(pagination.next_page_url)" >></a>
                </li>
                <li class="page-item next" >
                        <a class="page-scroll page-link" v-on:click="getResults(pagination.last_page_url)" >>></a>
                </li>
        </ul>
</div>
