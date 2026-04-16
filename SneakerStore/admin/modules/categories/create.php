<h1>Thêm danh mục</h1>

<form action="modules/categories/handle.php" method="post">
    <input type="hidden" name="action" value="create">

    <p>
        <label>Tên danh mục</label><br>
        <input type="text" name="name">
    </p>

    <p>
        <label>Thứ tự</label><br>
        <input type="number" name="sort_order" value="1">
    </p>

    <p>
        <label>Trạng thái</label><br>
        <select name="status">
            <option value="1">Hiện</option>
            <option value="0">Ẩn</option>
        </select>
    </p>

    <button type="submit">Lưu</button>
</form>