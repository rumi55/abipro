      
      <table class="table table-sm">
        <thead>
          <th colspan="2">{{__('Actions')}}</th>
          @foreach($roles as $role)
            @if($role->name!='super-admin')
            <th class="text-center">{{$role->display_name}}<i class="fas fa-help"></i></th>
            @endif
          @endforeach
        </thead>
        <tbody>
          @foreach($actions as $action)
            @foreach($action as $i => $p)
              @if($i==0)
              <tr>
                <td colspan="2" class="font-weight-bold">{{tt($p,'display_group')}}</td>
                @foreach($roles as $role)
                  @if($role->name!='super-admin')
                  <td class="text-center"></td>
                  @endif
                @endforeach    
              </tr>
              @endif
              <tr>
                <td style="width:10px;">{{$i+1}}.</td>
                <td  title="{{$p->description}}">{{tt($p,'display_name')}}</td>
                @foreach($roles as $role)
                  <td class="text-center">
                  @if($user->is_owner)
                  <div class="icheck-primary d-inline">
                    <input id="check-{{$role->id.'-'.$p->id}}" {{$user->is_owner?'':'disabled'}} type="checkbox" name="actions[]" value="{{$role->id.'-'.$p->id}}" {{array_key_exists(($role->id.'-'.$p->id),$user_group_actions)?'checked':''}} >
                    <label for="check-{{$role->id.'-'.$p->id}}"></label>
                  </div>
                  @else
                  <i class="text-primary fas {{array_key_exists(($role->id.'-'.$p->id),$user_group_actions)?'fa-check':'fa-minu'}}"></i>
                  @endif
                  </td>
                @endforeach    
              </tr>
            @endforeach
          @endforeach
        </tbody>
      </table>