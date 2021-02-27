import { __ } from '@wordpress/i18n'

const SettingsTable = ({domain, fields, settings}) => {
  return (<>
    <table className="form-table" role="presentation">
      <tbody>
        { fields.map((field, key) => (<tr className="d-flex flex-row" key={key}>
          <th scope="row">
            <label for={field.key}>{field.label}</label>
          </th>
          {field.fieldType == 'text' && (<td>
            <input type="text" id={field.key} autocomplete="off" name={`${domain}[${field.key}]`} value={ settings[field.key] } onChange={(e) => {field.onChange(field.key, e.target.value)}} />
            {!!field.description && <p className="description">{ field.description }</p>}
          </td>)}
        </tr>)) }
      </tbody>
    </table>
  </>)
}

export {
  SettingsTable
}