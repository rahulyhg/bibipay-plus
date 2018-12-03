<template>
  <div class="container-fluid" style="margin-top:10px;">
  <div class="panel panel-default">
    <div class="panel-body">
      <table class="table table-bordered">
        <thead>
          <tr>
            <th>收支</th>
            <th>币种</th>
            <th>金额</th>
            <th>详情</th>
            <th>时间</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="item in data">
            <td>{{item.less_number ? '支出' : '收入'}}</td>
            <td>{{item.type}}</td>
            <td>{{item.less_number ? item.less_number : item.add_number}}</td>
            <td>{{item.action_type}}</td>
            <td>{{item.created_time}}</td>
          </tr>
        </tbody>
      </table>
      <!-- 分页 -->
        <el-pagination
          background
          :page-size="15"
          @current-change="handleCurrentChange"
          layout="prev, pager, next"
          :total="total">
        </el-pagination>
    </div>
  </div>
</div>
</template>
<style scoped>
.dropbtn {
  border: none;
  cursor: pointer;
}
.dropdown {
  position: relative;
  display: inline-block;
}
.dropdown-content {
  display: none;
  position: absolute;
  background-color: #f9f9f9;
  min-width: 40px;
  box-shadow: 0px 8px 16px 0px rgba(0, 0, 0, 0.2);
}
.dropdown-content a {
  color: black;
  padding: 10px;
  text-decoration: none;
  display: block;
}
.dropdown-content a:hover {
  background-color: #f1f1f1;
}
.dropdown:hover .dropdown-content {
  display: block;
}
</style>


<script>
import lodash from "lodash";
import { mapGetters } from "vuex";
import request, { createRequestURI } from "../../util/request";

const AccountDetails = {
  /**
   * 定义当前组件状态数据
   *
   * @return {Object}
   * @author Seven Du <shiweidu@outlook.com>
   * @homepage http://medz.cn
   */
   data() {
     return {
       data:{},
       total:0
     }
   },
   mounted (){
     let id = this.$route.params.id;
     request.post(
        createRequestURI('account/log'),
        {user_id:id},
        { validateStatus: status => status === 200 }
        ).then(response => {
        console.log(response)
          this.data = response.data.data;
         this.total = response.data.total;
        }).catch(({ response: { data: { errors = ['加载认证类型失败'] } = {} } = {} }) => {
     });
   },
   methods:{
        handleCurrentChange(val){
           let id = this.$route.params.id;
           request.post(
               createRequestURI('account/log'),
               {page:val, user_id:id},
                {validateStatus: status => status === 200 }
             ).then(response => {
                 this.data = response.data.data;
                 this.total = parseInt(response.data.total)
             })
        }
   }

};
export default AccountDetails;
</script>
