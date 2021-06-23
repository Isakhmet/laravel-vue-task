<template>
  <div class="container">
          <div class="card-body">
            <datatable :columns="columns" :data="rows"></datatable>
          </div>
        </div>
</template>

<script>

export default {
  mounted() {
    console.log('Component mounted.')
  },
  data(){
    return {
      columns: [
        {label: 'id', field: 'id'},
        {label: 'Qliq', field: 'Qliq'},
        {label: 'Qoil', field: 'Qoil'},
        {label: 'date', field: 'date'},
        {label: 'company', field: 'company.name'}
      ],
      rows: [],
      page: 1,
      per_page: 10,
    }
  },
  methods:{
    getPosts: function() {
      axios.get('/fact').then(function(response){
        console.log(response.data)
        this.rows = response.data;
      }.bind(this));
    }
  },
  created: function(){
    this.getPosts()
  }
}
</script>

<style scoped>

</style>